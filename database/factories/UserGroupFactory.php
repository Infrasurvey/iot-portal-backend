<?php

namespace Database\Factories;

use App\Models\UserGroup;
use App\Models\User;
use App\Models\Group;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->limit(1)->get()[0]->id,
            'group_id' => Group::inRandomOrder()->limit(1)->get()[0]->id,
            'is_group_admin' => true
        ];
    }
}
