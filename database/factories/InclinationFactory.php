<?php

namespace Database\Factories;

use App\Models\Inclination;
use App\Models\DeviceRover;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class InclinationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Inclination::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $raw_acceleration_norm = rand(1280, 1320);
        $raw_acceleration_x = rand(0, 50);
        $raw_acceleration_y = rand(-1250, -1100);
        $raw_acceleration_z = sqrt((pow($raw_acceleration_norm, 2) - pow($raw_acceleration_x, 2) - pow($raw_acceleration_y, 2)));

        $acceleration_norm = 9.81;
        $acceleration_x = $raw_acceleration_x/$raw_acceleration_norm * $acceleration_norm;
        $acceleration_y = $raw_acceleration_y/$raw_acceleration_norm * $acceleration_norm;
        $acceleration_z = $raw_acceleration_z/$raw_acceleration_norm * $acceleration_norm;

        return [
            'device_rover_id' => DeviceRover::inRandomOrder()->limit(1)->get()[0]->id,
            'file_id' => File::inRandomOrder()->limit(1)->get()[0]->id,
            'raw_acceleration_x' => $raw_acceleration_x,
            'raw_acceleration_y' => $raw_acceleration_y,
            'raw_acceleration_z' => $raw_acceleration_z,
            'raw_acceleration_norm' => $raw_acceleration_norm,
            'acceleration_x' => $acceleration_x,
            'acceleration_y' => $acceleration_y,
            'acceleration_z' => $acceleration_z,
            'acceleration_norm' => $acceleration_norm,
            'angle_x' => 0,
            'angle_y' => 0,
        ];
    }
}
