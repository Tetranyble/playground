<?php

namespace Database\Factories;

use App\Enums\TrilioStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement([
                TrilioStatus::PENDING->value,
                TrilioStatus::COMPLETED->value,
                TrilioStatus::INPROGRESS->value,
            ]),
            'slug' => $this->faker->slug,
            'uuid' => $this->faker->uuid,
            'start_date' => $this->faker->dateTime,
            'end_date' => $this->faker->dateTime,
        ];
    }
}
