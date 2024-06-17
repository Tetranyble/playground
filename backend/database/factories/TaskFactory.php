<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\TrilioStatus;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'priority' => $this->faker->randomElement([
                Priority::LOW->value,
                Priority::HIGH->value,
                Priority::MEDIUM->value,
            ]),
            'status' => $this->faker->randomElement([
                TrilioStatus::PENDING->value,
                TrilioStatus::COMPLETED->value,
                TrilioStatus::INPROGRESS->value,
            ]),
            'activity_id' => Activity::factory(),
            'due_date' => now(),
        ];
    }
}
