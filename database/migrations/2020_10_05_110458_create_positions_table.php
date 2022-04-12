<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_rover_id')->constrained();
            $table->foreignId('file_id')->constrained();
            $table->double('height');
            $table->double('latitude');
            $table->double('longitude');
            $table->double('nbr_of_samples');
            $table->double('nbr_of_samples_where_q_equal_1');
            $table->double('nbr_of_satellites');
            $table->enum('validity', ['valid', 'no_samples', 'no_samples_with_q_equal_1']);
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
        Schema::dropIfExists('positions');
    }
}
