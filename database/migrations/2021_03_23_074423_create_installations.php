<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreignId('device_base_station_id')->constrained();
            $table->string('name')->nullable();
            $table->boolean('active')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamp('installation_date', 0)->nullable();
            $table->timestamp('last_human_intervention', 0)->nullable();
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
        Schema::dropIfExists('installations');
    }
}
