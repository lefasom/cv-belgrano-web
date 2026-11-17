<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title_obtained' => $this->faker->sentence(3),
            'institution' => $this->faker->company(),
            'period' => $this->faker->numberBetween(2010, 2023) . ' - ' . $this->faker->numberBetween(2024, 2026),
            'status' => $this->faker->randomElement(['Completed', 'In Progress']),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}

