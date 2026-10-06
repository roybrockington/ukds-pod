<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    /**
     * Show the booking form.
     */
    public function create(): Response
    {
        $booked = Booking::bookedTimeslots();

        return Inertia::render('SignUp', [
            'days' => collect(Booking::timeslots())->map(fn (array $slots, string $day) => [
                'label' => Carbon::parse($day)->format('l jS F'),
                'slots' => collect($slots)->map(fn (Carbon $slot) => [
                    'value' => $slot->format('Y-m-d H:i'),
                    'label' => $slot->format('g:ia'),
                    'booked' => $booked->contains($slot->format('Y-m-d H:i')),
                ]),
            ])->values(),
        ]);
    }

    /**
     * Store a new booking request.
     */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        Booking::create($request->validated());

        return to_route('bookings.success');
    }

    /**
     * Show the booking request confirmation.
     */
    public function success(): Response
    {
        return Inertia::render('BookingSuccess');
    }
}
