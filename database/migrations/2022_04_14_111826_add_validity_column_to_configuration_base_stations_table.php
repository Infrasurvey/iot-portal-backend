<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidityColumnToConfigurationBaseStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuration_base_stations', function (Blueprint $table) {
            $table->enum('validity', ['valid', 'corrupted']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuration_base_stations', function (Blueprint $table) {
            $table->dropColumn('validity');
        });
    }
}
