<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRequested extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Booking $booking)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $slot = $booking->timeslot->format('l jS F \a\t g:ia');

        return (new MailMessage)
            ->subject("New podcast booking request: {$slot}")
            ->replyTo($booking->email, $booking->name)
            ->greeting('New booking request')
            ->line("{$booking->name} has requested the {$slot} slot.")
            ->line("**Email:** {$booking->email}")
            ->when($booking->phone, fn (MailMessage $mail) => $mail->line("**Phone:** {$booking->phone}"))
            ->when($booking->subject, fn (MailMessage $mail) => $mail->line("**Podcast subject:** {$booking->subject}"))
            ->when($booking->instagram, fn (MailMessage $mail) => $mail->line("**Instagram:** @{$booking->instagram}"))
            ->action('Review bookings', route('dashboard'))
            ->line('The slot is held until you confirm or decline the request. Reply to this email to contact the booker directly.');
    }
}
