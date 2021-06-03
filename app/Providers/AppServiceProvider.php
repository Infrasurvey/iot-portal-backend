<?php

namespace App\Providers;

use DB;
use Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /* DB::listen(function($query) {
            Log::info(
                $query->sql,
                $query->bindings,
                $query->time
            );
        }); */

        Relation::morphMap([
            'device_rovers' => 'App\Models\DeviceRover',
            'device_base_stations' => 'App\Models\DeviceBaseStation'
        ]);
    }
}
