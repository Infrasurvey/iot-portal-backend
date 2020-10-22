<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasureRoversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measure_rovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_rover_id')->constrained();
            $table->foreignId('file_id')->constrained();
            $table->bigInteger('rssi');
            $table->double('raw_acceleration_x');
            $table->double('raw_acceleration_y');
            $table->double('raw_acceleration_z');
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
        Schema::dropIfExists('measure_rovers');
    }
}
