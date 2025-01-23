<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'code' => $this->faker->word(),
            'name' => $this->faker->word(),
            'location' => $this->faker->city(),
            'address' => $this->faker->address(),
            'licenseNumber' => $this->faker->word(),
            'licenseDate' => Carbon::parse($this->faker->date()),
            'company_code' => $this->faker->word(),
            'appraised_lot_value' => $this->faker->numberBetween(2000.0,5000.0),
        ];
    }
}
