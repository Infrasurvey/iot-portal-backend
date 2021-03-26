<?php

namespace Database\Factories;

use App\Models\Installation;
use App\Models\Group;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InstallationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Installation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'group_id' => Group::inRandomOrder()->limit(1)->get()[0]->id,
            'name' => $this->faker->name,
            'image_path' => 'thisisapath',
            'installation_date' => now(),
            'last_human_intervention'=>now()
        ];
    }
}
