<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class ChangeColumnsToNullableInMeasureRoversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }

        Schema::table('measure_rovers', function (Blueprint $table) {
            $table->bigInteger('rssi')->nullable(true)->change();
            $table->double('raw_acceleration_x')->nullable(true)->change();
            $table->double('raw_acceleration_y')->nullable(true)->change();
            $table->double('raw_acceleration_z')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('measure_rovers', function (Blueprint $table) {
            $table->bigInteger('rssi')->nullable(false)->change();
            $table->double('raw_acceleration_x')->nullable(false)->change();
            $table->double('raw_acceleration_y')->nullable(false)->change();
            $table->double('raw_acceleration_z')->nullable(false)->change();
        });
    }
}
