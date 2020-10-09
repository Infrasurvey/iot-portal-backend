<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\DeviceBaseStation;
use App\Models\DeviceRover;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Device::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $random = rand(1,2);
        $batteryVoltage = rand(9,14);

        if ($random == 1)
        {
            $table_id = DeviceBaseStation::inRandomOrder()->limit(1)->get()[0]->id;
            $table_name = "device_base_stations";
        }
        else
        {
            $table_id = DeviceRover::inRandomOrder()->limit(1)->get()[0]->id;
            $table_name = "device_rovers";
        }
        
        return [
            'file_id' => File::inRandomOrder()->limit(1)->get()[0]->id,
            'table_type' => $table_name,
            'table_id' => $table_id,
            'battery_voltage' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 9, $max = 14),
            'firmware_version' => $this->faker->randomDigit
        ];
    }
}
