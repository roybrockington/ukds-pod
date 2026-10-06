<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Notifications\BookingConfirmed;
use Illuminate\Http\RedirectResponse;

class ConfirmBookingController extends Controller
{
    /**
     * Mark a booking request as confirmed and let the booker know.
     */
    public function __invoke(Booking $booking): RedirectResponse
    {
        if ($booking->confirmed) {
            return back();
        }

        $booking->confirmed = true;
        $booking->save();

        // A mail failure shouldn't undo the confirmation.
        rescue(fn () => $booking->notify(new BookingConfirmed));

        return back();
    }
}
