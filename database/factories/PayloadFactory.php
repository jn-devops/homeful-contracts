<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Mapping;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payload>
 */
class PayloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mapping_code' => Mapping::factory()->create()->code,
            'value' => $this->faker->word()
        ];
    }
}
