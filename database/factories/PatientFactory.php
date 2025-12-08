<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'contact_number' => $this->faker->phoneNumber(),
            'age' => $this->faker->numberBetween(18, 80),
            'sex' => $this->faker->randomElement(['Male', 'Female']),

            // 'frame_type' => $this->faker->randomElement(['Metal', 'Plastic', 'Rimless', null]),
            // 'color' => $this->faker->safeColorName(),
            // 'lens_supply' => $this->faker->randomElement(['Single Vision', 'Bifocal', 'Progressive', null]),
            'diagnosis' => $this->faker->sentence(3),

            'special_instructions' => $this->faker->randomElement(['Wear full-time', 'Use for reading', null]),
            'follow_up_on' => $this->faker->optional()->dateTimeBetween('+1 week', '+6 months'),

            // 'amount' => $this->faker->randomFloat(2, 500, 5000),
            // 'deposit' => $this->faker->randomFloat(2, 100, 2500),
            // 'balance' => fn(array $attributes) => $attributes['amount'] - $attributes['deposit'],

            'created_by' => $this->faker->randomElement([1, 2]), // Always assigned to user ID 1
            'archived_at' => null,
        ];
    }
}
