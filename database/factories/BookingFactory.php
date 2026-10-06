<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'timeslot' => fake()->randomElement(Arr::flatten(Booking::timeslots())),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'subject' => fake()->optional()->sentence(4),
            'instagram' => fake()->optional()->userName(),
            'confirmed' => false,
        ];
    }

    /**
     * Indicate that the booking has been approved by an admin.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed' => true,
        ]);
    }
}
