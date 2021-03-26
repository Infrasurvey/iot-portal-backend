<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(1)->create();
        \App\Models\Organization::factory(5)->create();
        \App\Models\Group::factory(5)->create();
        \App\Models\Installation::factory(5)->create();
        \App\Models\UserGroup::factory(5)->create();

       /* \App\Models\File::factory(10)->create();
        \App\Models\DeviceBaseStation::factory(15)->create();
        \App\Models\DeviceRover::factory(100)->create();
        \App\Models\Device::factory(115)->create();
        \App\Models\Position::factory(100)->create();*/
    }
}
