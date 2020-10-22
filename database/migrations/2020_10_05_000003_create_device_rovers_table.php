<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceRoversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_rovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_base_station_id')->constrained();
            $table->string('unique_id')->nullable();
            $table->double('coordinate_x')->nullable();
            $table->double('coordinate_y')->nullable();
            $table->double('coordinate_z')->nullable();
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
        Schema::dropIfExists('device_rovers');
    }
}
