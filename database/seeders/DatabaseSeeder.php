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
        \App\Models\User::factory()->create([
            'name' => 'admin',
            'lastname' => '',
            'address' => 'Route de Cheseaux 1',
            'zip' => '1400',
            'city' => 'Yverdon-les-Bains',
            'country' => 'CH',
            'phone' => '024 557 63 15',
            'language' => 'English',
            'email' => 'admin@geomon.ch',
            'is_admin' => true,
            'password' => '$2y$10$y4wzDc/ezkq1Nb5QsUGhpO9skPbBu6H.Ne1ZO2H6HuRPzl0EPoVKq', // password
        ]);
        //-------------------Organization--------------------------------------
        \App\Models\Organization::factory()->create([
            'name' => 'OFEV'
        ]);
        \App\Models\Organization::factory()->create([
            'name' => 'UNI Grenoble'
        ]);
        \App\Models\Organization::factory()->create([
            'name' => 'Uni Strasbourg'
        ]);
        //-------------------Group--------------------------------------
        \App\Models\Group::factory()->create([
            'organization_id' => 1,
            'name' => 'Fribourg'
        ]);
        \App\Models\Group::factory()->create([
            'organization_id' => 1,
            'name' => 'Valais'
        ]);
        \App\Models\Group::factory()->create([
            'organization_id' => 1,
            'name' => 'Grison'
        ]);
        \App\Models\Group::factory()->create([
            'organization_id' => 2,
            'name' => 'Group 1 Grenoble'
        ]);
        \App\Models\Group::factory()->create([
            'organization_id' => 3,
            'name' => 'Group 1 Strasbourg'
        ]);

        //-------------------Installation--------------------------------------
        /*\App\Models\Installation::factory()->create([
            'group_id' => 1,
            'device_base_station_id' => 1,
            'name' => 'Hohberg',
            'active' => true,
            'image_path' => 'default_image.png',
            'installation_date' => now(),
            'last_human_intervention'=>now()
        ]);
        \App\Models\Installation::factory()->create([
            'group_id' => 2,
            'device_base_station_id' => 2,
            'name' => 'Derborence',
            'active' => true,
            'image_path' => 'default_image.png',
            'installation_date' => now(),
            'last_human_intervention'=>now()
        ]);
        \App\Models\Installation::factory()->create([
            'group_id' => 2,
            'device_base_station_id' => 3,
            'name' => 'Saas 2 Grueebu',
            'active' => true,
            'image_path' => 'default_image.png',
            'installation_date' => now(),
            'last_human_intervention'=>now()
        ]);
        \App\Models\Installation::factory()->create([
            'group_id' => 2,
            'device_base_station_id' => 4,
            'name' => 'Saas 2 Seewjine',
            'active' => true,
            'image_path' => 'default_image.png',
            'installation_date' => now(),
            'last_human_intervention'=>now()
        ]);
        \App\Models\Installation::factory()->create([
            'group_id' => 3,
            'device_base_station_id' => 5,
            'name' => 'Pontresina',
            'active' => true,
            'image_path' => 'default_image.png',
            'installation_date' => now(),
            'last_human_intervention'=>now()
        ]);
        \App\Models\Installation::factory()->create([
            'group_id' => 5,
            'device_base_station_id' => 6,
            'name' => 'Aiguilles',
            'active' => true,
            'image_path' => 'default_image.png',
            'installation_date' => now(),
            'last_human_intervention'=>now()
        ]);
        //\App\Models\UserGroup::factory(5)->create();

       /* \App\Models\File::factory(10)->create();
        \App\Models\DeviceBaseStation::factory(15)->create();
        \App\Models\DeviceRover::factory(100)->create();
        \App\Models\Device::factory(115)->create();
        \App\Models\Position::factory(100)->create();*/
    }
}
