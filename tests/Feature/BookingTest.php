<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Notifications\BookingRequested;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Whether the faked Cloudflare siteverify API accepts the token.
     */
    protected bool $turnstilePasses = true;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.turnstile.secret' => 'test-secret']);

        Http::fake([
            'challenges.cloudflare.com/*' => fn () => Http::response(['success' => $this->turnstilePasses]),
        ]);
    }

    public function test_the_booking_form_lists_the_available_timeslots(): void
    {
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->component('SignUp')
            ->has('days', 2)
            ->where('days.0.label', 'Saturday 24th October')
            ->has('days.0.slots', 18)
            ->where('days.0.slots.0', ['value' => '2026-10-24 09:15', 'label' => '9:15am', 'booked' => false])
            ->where('days.1.slots.17', ['value' => '2026-10-25 17:45', 'label' => '5:45pm', 'booked' => false])
        );
    }

    public function test_a_booking_can_be_requested(): void
    {
        $response = $this->post('/bookings', [
            'turnstile' => 'test-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
            'phone' => '07700 900123',
            'subject' => 'Community radio',
            'instagram' => '@jane.doe',
        ]);

        $response->assertRedirect(route('bookings.success'));

        $booking = Booking::sole();
        $this->assertSame('Jane Doe', $booking->name);
        $this->assertSame('2026-10-24 09:15', $booking->timeslot->format('Y-m-d H:i'));
        $this->assertSame('jane.doe', $booking->instagram);
        $this->assertFalse($booking->confirmed);
    }

    public function test_optional_fields_can_be_left_blank(): void
    {
        $this->post('/bookings', [
            'turnstile' => 'test-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-25 17:45',
            'email' => 'jane@example.com',
            'phone' => '',
            'subject' => '',
            'instagram' => '',
        ])->assertRedirect(route('bookings.success'));

        $booking = Booking::sole();
        $this->assertNull($booking->phone);
        $this->assertNull($booking->subject);
        $this->assertNull($booking->instagram);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post('/bookings', [])
            ->assertSessionHasErrors(['name', 'timeslot', 'email']);

        $this->assertDatabaseEmpty('bookings');
    }

    public function test_timeslots_outside_the_schedule_are_rejected(): void
    {
        foreach (['2026-10-24 09:00', '2026-10-24 18:15', '2026-10-26 10:15'] as $timeslot) {
            $this->post('/bookings', [
                'turnstile' => 'test-token',
                'name' => 'Jane Doe',
                'timeslot' => $timeslot,
                'email' => 'jane@example.com',
            ])->assertSessionHasErrors('timeslot');
        }

        $this->assertDatabaseEmpty('bookings');
    }

    public function test_a_booking_cannot_confirm_itself(): void
    {
        $this->post('/bookings', [
            'turnstile' => 'test-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
            'confirmed' => true,
        ]);

        $this->assertFalse(Booking::sole()->confirmed);
    }

    public function test_the_success_page_can_be_rendered(): void
    {
        $this->get('/bookings/success')
            ->assertInertia(fn (Assert $page) => $page->component('BookingSuccess'));
    }

    public function test_booked_timeslots_are_marked_on_the_form(): void
    {
        Booking::factory()->create(['timeslot' => '2026-10-24 09:45']);
        Booking::factory()->confirmed()->create(['timeslot' => '2026-10-25 10:15']);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('days.0.slots.0.booked', false)
            ->where('days.0.slots.1.booked', true)
            ->where('days.1.slots.2.booked', true)
        );
    }

    public function test_a_booked_timeslot_cannot_be_requested_again(): void
    {
        Booking::factory()->create(['timeslot' => '2026-10-24 09:15']);

        $this->post('/bookings', [
            'turnstile' => 'test-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
        ])->assertSessionHasErrors('timeslot');

        $this->assertSame(1, Booking::count());
    }

    public function test_admins_are_notified_when_a_booking_is_requested(): void
    {
        Notification::fake();
        config(['mail.admins' => ['one@example.com', 'two@example.com']]);

        $this->post('/bookings', [
            'turnstile' => 'test-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
            'subject' => 'Community radio',
        ]);

        Notification::assertSentOnDemand(
            BookingRequested::class,
            function (BookingRequested $notification, array $channels, AnonymousNotifiable $notifiable) {
                $mail = $notification->toMail($notifiable);

                return $notifiable->routes['mail'] === ['one@example.com', 'two@example.com']
                    && $notification->booking->is(Booking::sole())
                    && $mail->subject === 'New podcast booking request: Saturday 24th October at 9:15am'
                    && $mail->replyTo === [['jane@example.com', 'Jane Doe']];
            }
        );
    }

    public function test_no_notification_is_sent_without_admin_addresses(): void
    {
        Notification::fake();
        config(['mail.admins' => []]);

        $this->post('/bookings', [
            'turnstile' => 'test-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
        ])->assertRedirect(route('bookings.success'));

        Notification::assertNothingSent();
    }

    public function test_a_mail_failure_does_not_stop_the_booking(): void
    {
        config(['mail.admins' => ['one@example.com'], 'mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);

        $this->post('/bookings', [
            'turnstile' => 'test-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
        ])->assertRedirect(route('bookings.success'));

        $this->assertSame(1, Booking::count());
    }

    public function test_the_turnstile_token_is_verified_with_cloudflare(): void
    {
        $this->post('/bookings', [
            'turnstile' => 'test-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
        ])->assertRedirect(route('bookings.success'));

        Http::assertSent(fn (Request $request) => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request['secret'] === 'test-secret'
            && $request['response'] === 'test-token');
    }

    public function test_a_booking_without_a_turnstile_token_is_rejected(): void
    {
        $this->post('/bookings', [
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
        ])->assertSessionHasErrors('turnstile');

        Http::assertNothingSent();
        $this->assertDatabaseEmpty('bookings');
    }

    public function test_a_booking_that_fails_turnstile_is_rejected(): void
    {
        $this->turnstilePasses = false;

        $this->post('/bookings', [
            'turnstile' => 'bad-token',
            'name' => 'Jane Doe',
            'timeslot' => '2026-10-24 09:15',
            'email' => 'jane@example.com',
        ])->assertSessionHasErrors('turnstile');

        $this->assertDatabaseEmpty('bookings');
    }

    public function test_the_form_receives_the_turnstile_site_key(): void
    {
        config(['services.turnstile.site_key' => 'test-site-key']);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('turnstileSiteKey', 'test-site-key')
        );
    }
}
