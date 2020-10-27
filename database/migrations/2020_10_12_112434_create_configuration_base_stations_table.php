<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationBaseStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_base_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_base_station_id')->constrained();
            $table->foreignId('file_id')->constrained();
            $table->boolean('continuous_mode')->nullable();
            $table->boolean('reset')->nullable();
            $table->bigInteger('wakeup_period_in_minutes')->nullable();
            $table->time('session_start_time')->nullable();
            $table->bigInteger('session_period_in_wakeup_period')->nullable();
            $table->bigInteger('session_duration_in_minutes')->nullable();
            $table->boolean('non_continuous_store_binr_to_ftp')->nullable();
            $table->bigInteger('reference_gps_module')->nullable();
            $table->double('reference_latitude')->nullable();
            $table->double('reference_longitude')->nullable();
            $table->double('reference_altitude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_base_stations');
    }
}
