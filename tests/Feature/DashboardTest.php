<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_non_admins_cannot_view_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_admins_see_every_slot_with_its_bookings(): void
    {
        $pending = Booking::factory()->create(['timeslot' => '2026-10-24 09:15', 'name' => 'Pending Person']);
        $confirmed = Booking::factory()->confirmed()->create(['timeslot' => '2026-10-24 09:15', 'name' => 'Confirmed Person']);
        Booking::factory()->create(['timeslot' => '2026-10-25 17:45']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/dashboard')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('days', 2)
                ->where('days.0.label', 'Saturday 24th October')
                ->has('days.0.slots', 18)
                ->where('days.0.slots.0.time', '9:15am')
                ->has('days.0.slots.0.bookings', 2)
                ->where('days.0.slots.0.bookings.0.id', $pending->id)
                ->where('days.0.slots.0.bookings.0.confirmed', false)
                ->where('days.0.slots.0.bookings.1.id', $confirmed->id)
                ->where('days.0.slots.0.bookings.1.confirmed', true)
                ->has('days.0.slots.1.bookings', 0)
                ->has('days.1.slots.17.bookings', 1)
            );
    }

    public function test_admins_can_confirm_a_booking(): void
    {
        $booking = Booking::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->from('/dashboard')
            ->patch(route('bookings.confirm', $booking))
            ->assertRedirect('/dashboard');

        $this->assertTrue($booking->fresh()->confirmed);
    }

    public function test_non_admins_cannot_confirm_a_booking(): void
    {
        $booking = Booking::factory()->create();

        $this->actingAs(User::factory()->create())
            ->patch(route('bookings.confirm', $booking))
            ->assertForbidden();

        $this->patch(route('bookings.confirm', $booking));

        $this->assertFalse($booking->fresh()->confirmed);
    }

    public function test_admins_can_remove_a_booking(): void
    {
        $pending = Booking::factory()->create(['timeslot' => '2026-10-24 09:15']);
        $confirmed = Booking::factory()->confirmed()->create(['timeslot' => '2026-10-24 09:45']);
        $admin = User::factory()->admin()->create();

        foreach ([$pending, $confirmed] as $booking) {
            $this->actingAs($admin)
                ->from('/dashboard')
                ->delete(route('bookings.destroy', $booking))
                ->assertRedirect('/dashboard');

            $this->assertModelMissing($booking);
        }

        $this->assertEmpty(Booking::bookedTimeslots());
    }

    public function test_non_admins_cannot_remove_a_booking(): void
    {
        $booking = Booking::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('bookings.destroy', $booking))
            ->assertForbidden();

        $this->assertModelExists($booking);
    }
}
