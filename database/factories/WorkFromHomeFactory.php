<?php

namespace Database\Factories;

use App\Models\WorkFromHome;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkFromHomeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkFromHome::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('now', '+2 months')->format('d-m-Y'),
            'hours' => $this->faker->numberBetween(1, 20),
        ];
    }
}
