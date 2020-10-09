<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\DeviceRover;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Position::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $nbr_of_samples = rand(20, 50);

        return [
            'device_rover_id' => DeviceRover::inRandomOrder()->limit(1)->get()[0]->id,
            'file_id' => File::inRandomOrder()->limit(1)->get()[0]->id,
            'height' => rand(600, 2000),
            'latitude' => rand(46000000000, 48000000000) / 1000000000,
            'longitude' => rand(7000000000, 9000000000) / 1000000000,
            'nbr_of_samples' => $nbr_of_samples,
            'nbr_of_samples_where_q_equal_1' => $nbr_of_samples - 10,
            'nbr_of_satellites' => rand(10,25)
        ];
    }
}
