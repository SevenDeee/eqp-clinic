<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prescription>
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Prescription::class;

    public function definition(): array
    {
        $jsonTemplate = [
            'od' => [
                'sphere' => $this->faker->randomElement(['-1.25', '-0.75', '-2.00']),
                'cylinder' => $this->faker->randomElement(['-0.50', '-0.25', '-1.00']),
                'axis' => $this->faker->numberBetween(1, 180),
                'monopd' => $this->faker->numberBetween(28, 34),
            ],
            'os' => [
                'sphere' => $this->faker->randomElement(['-1.00', '-0.50', '-1.75']),
                'cylinder' => $this->faker->randomElement(['-0.25', '-0.75', '-1.00']),
                'axis' => $this->faker->numberBetween(1, 180),
                'monopd' => $this->faker->numberBetween(28, 34),
            ],
        ];

        return [
            'patient_id' => Patient::factory(), // automatically creates a patient
            'far' => $jsonTemplate,
            'near' => $jsonTemplate,
            'remarks' => $this->faker->randomElement(['N/A', 'Follow up needed', 'Adjust prescription']),
            'prescribed_by' => $this->faker->randomElement([1, 2]),
        ];
    }
}
