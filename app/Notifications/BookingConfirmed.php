<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification
{
    use Queueable;

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
    public function toMail(Booking $booking): MailMessage
    {
        $slot = $booking->timeslot->format('l jS F \a\t g:ia');

        return (new MailMessage)
            ->subject("Your podcast slot is confirmed: {$slot}")
            ->when(config('mail.admins'), fn (MailMessage $mail, array $admins) => $mail->replyTo($admins))
            ->greeting("Hi {$booking->name},")
            ->line("Good news! Your podcast slot on **{$slot}** has been confirmed.")
            ->when($booking->subject, fn (MailMessage $mail) => $mail->line("**Podcast subject:** {$booking->subject}"))
            ->line('Please arrive a few minutes before your slot. If you need to change or cancel your booking, please give us as much notice as possible.')
            ->line('We look forward to podcasting with you!');
    }
}
