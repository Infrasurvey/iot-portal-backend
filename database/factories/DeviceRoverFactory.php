<?php

namespace Database\Factories;

use App\Models\DeviceRover;
use App\Models\DeviceBaseStation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceRoverFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DeviceRover::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'device_base_station_id' => DeviceBaseStation::inRandomOrder()->limit(1)->get()[0]->id,
            'gps_id' => rand(1, 15),
            'rssi' => rand(-100, -60)
        ];
    }
}
