<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
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
            'user_id' => $this->faker->randomElement(User::all())->id,
            'date' => $this->faker->date(),
            'type' => $this->faker->randomElement(['in', 'out']),
            'destanation' => $this->faker->randomElement(['paris', 'tokyo', 'london','new york','singapore','mumbai','sydney']),
        ];
    }
}
