<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

use App\Models\Basestation;

class BasestationFactory extends Factory
{
    protected $model = Basestation::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'identificateur' => $this->faker->unique()->randomNumber()
        ];
    }
};