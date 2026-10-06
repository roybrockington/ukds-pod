<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;

class DeleteBookingController extends Controller
{
    /**
     * Remove a booking, freeing up its timeslot.
     */
    public function __invoke(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return back();
    }
}
