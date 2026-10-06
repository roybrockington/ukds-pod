<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the schedule of slots along with their booking requests.
     */
    public function __invoke(): Response
    {
        $bookings = Booking::query()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (Booking $booking) => $booking->timeslot->format('Y-m-d H:i'));

        return Inertia::render('Dashboard', [
            'days' => collect(Booking::timeslots())->map(fn (array $slots, string $day) => [
                'label' => Carbon::parse($day)->format('l jS F'),
                'slots' => collect($slots)->map(fn (Carbon $slot) => [
                    'time' => $slot->format('g:ia'),
                    'bookings' => $bookings->get($slot->format('Y-m-d H:i'), collect())
                        ->map(fn (Booking $booking) => $booking->only([
                            'id', 'name', 'email', 'phone', 'subject', 'instagram', 'confirmed',
                        ]))
                        ->values(),
                ]),
            ])->values(),
        ]);
    }
}
