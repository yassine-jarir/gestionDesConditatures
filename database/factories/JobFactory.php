<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" =>$this->faker->name(),
            "description" =>$this->faker->sentence(),
            "location" => $this->faker->city(),
            "salary" =>$this->faker->randomFloat(2,10000,50000),
            "company" =>$this->faker->company()
        ];
    }
}
