<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'admin',
            'lastname' => 'admin lastname',
            'address' => 'Rue du Moulin',
            'zip' => '1400',
            'city' => 'Yverdon-les-Bains',
            'country' => 'CH',
            'phone' => '089 934 34 34',
            'language' => 'English',
            'email' => 'admin@geomon.ch',
            'is_admin' => true,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}
