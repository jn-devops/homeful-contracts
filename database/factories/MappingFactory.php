<?php

namespace Database\Factories;

use App\Enums\{MappingCategory, MappingSource, MappingType};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mapping>
 */
class MappingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(),
            'path' => $this->faker->filePath(),
            'source' => MappingSource::random()->value,
            'title' => $this->faker->sentence(),
            'type' => MappingType::random()->value,
            'default' => $this->faker->sentence(),
            'category' => MappingCategory::random()->value,
            'transformer' => $this->faker->workd(),
            'options' => $this->faker->rgbColorAsArray(),
            'remarks' => $this->faker->sentence(),
        ];
    }
}
