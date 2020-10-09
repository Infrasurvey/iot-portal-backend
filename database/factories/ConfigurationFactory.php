<?php

namespace Database\Factories;

use App\Models\Configuration;
use App\Models\File;
use App\Models\DeviceBaseStation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConfigurationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Configuration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'device_base_station_id' => DeviceBaseStation::inRandomOrder()->limit(1)->get()[0]->id,
            'file_id' => File::inRandomOrder()->limit(1)->get()[0]->id,
            'continuous_mode' => rand(0, 1),
            'reset' => rand(0, 1),
            'wakeup_period_in_minutes' => rand(0, 65535),
            'session_start_time' => $this->faker->time($format = 'H:i:s', $max = '23:59:59'),
            'session_period_in_wakeup_period' => rand(1, 255),
            'session_duration_in_minutes' => rand(1, 255),
            'reference_gps_module' => rand(0, 15),
            'reference_latitude' => 46,
            'reference_longitude' => 7,
            'reference_altitude' => rand(300, 3000)
        ];
    }
}