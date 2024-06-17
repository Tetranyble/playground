<?php

namespace Database\Factories;

use App\Enums\Disk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => $this->faker->imageUrl,
            'uuid' => $this->faker->uuid,
            'description' => $this->faker->sentence,
            'disk' => Disk::PRIVATE->value,
            'attribution' => $this->faker->sentence,
            'mime_type' => 'image/png',
            'current' => $this->faker->randomElement([true, false]),
            'size' => $this->faker->numberBetween(3000, 40000),
        ];
    }
}
