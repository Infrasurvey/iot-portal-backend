<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInclinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inclinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_rover_id')->constrained();
            $table->foreignId('file_id')->constrained();
            $table->double('raw_acceleration_x');
            $table->double('raw_acceleration_y');
            $table->double('raw_acceleration_z');
            $table->double('raw_acceleration_norm');
            $table->double('acceleration_x');
            $table->double('acceleration_y');
            $table->double('acceleration_z');
            $table->double('acceleration_norm');
            $table->double('angle_x');
            $table->double('angle_y');
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
        Schema::dropIfExists('inclinations');
    }
}
