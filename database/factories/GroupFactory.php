<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Organization;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'organization_id' => Organization::inRandomOrder()->limit(1)->get()[0]->id,
            'name' => $this->faker->city
        ];
    }
}
