<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_base_station_id')->constrained();
            $table->foreignId('file_id')->constrained();
            $table->boolean('continuous_mode');
            $table->boolean('reset');
            $table->bigInteger('wakeup_period_in_minutes');
            $table->time('session_start_time');
            $table->bigInteger('session_period_in_wakeup_period');
            $table->bigInteger('session_duration_in_minutes');
            $table->bigInteger('reference_gps_module');
            $table->double('reference_latitude');
            $table->double('reference_longitude');
            $table->double('reference_altitude');
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
        Schema::dropIfExists('configurations');
    }
}
