<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;

class ConfirmBookingController extends Controller
{
    /**
     * Mark a booking request as confirmed.
     */
    public function __invoke(Booking $booking): RedirectResponse
    {
        $booking->confirmed = true;
        $booking->save();

        return back();
    }
}
