<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'position' => $this->faker->jobTitle(),
            'period' => $this->faker->numberBetween(2010, 2023).' - '.$this->faker->numberBetween(2024, 2026),
            'description' => $this->faker->paragraph(),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
