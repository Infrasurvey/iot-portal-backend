<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

use App\Models\Basestation;
use App\Models\Rower;

class RowerFactory extends Factory
{
    protected $model = Rower::class;

    public function definition()
    {
        $base_id = Basestation::inRandomOrder()->limit(1)->get()[0]->id;

        return [
            'name' => $this->faker->sentence(),
            'basestation_id' => $base_id
        ];
    }
};