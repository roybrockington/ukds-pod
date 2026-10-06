<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

#[Fillable(['name', 'timeslot', 'email', 'phone', 'subject', 'instagram'])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /**
     * The dates on which podcast slots are available.
     */
    public const DAYS = ['2026-10-24', '2026-10-25'];

    /**
     * Get every bookable slot, keyed by day. Slots start at quarter past and
     * quarter to the hour, from 9:15am to 5:45pm.
     *
     * @return array<string, list<Carbon>>
     */
    public static function timeslots(): array
    {
        return collect(self::DAYS)->mapWithKeys(fn (string $day) => [
            $day => collect(range(9, 17))
                ->crossJoin([15, 45])
                ->map(fn (array $time) => Carbon::parse($day)->setTime(...$time))
                ->all(),
        ])->all();
    }

    /**
     * Get the timeslots that already have a booking, confirmed or not.
     *
     * @return Collection<int, string>
     */
    public static function bookedTimeslots(): Collection
    {
        return self::query()
            ->pluck('timeslot')
            ->map(fn (Carbon $timeslot) => $timeslot->format('Y-m-d H:i'))
            ->unique()
            ->values();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'timeslot' => 'datetime',
            'confirmed' => 'boolean',
        ];
    }
}
