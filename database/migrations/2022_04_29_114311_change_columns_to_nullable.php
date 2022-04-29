<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class ChangeColumnsToNullable extends Migration
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

        Schema::table('positions', function (Blueprint $table) {
            $table->double('height')->nullable(true)->change();
            $table->double('latitude')->nullable(true)->change();
            $table->double('longitude')->nullable(true)->change();
            $table->double('nbr_of_samples')->nullable(true)->change();
            $table->double('nbr_of_samples_where_q_equal_1')->nullable(true)->change();
            $table->double('nbr_of_satellites')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->double('height')->nullable(false)->change();
            $table->double('latitude')->nullable(false)->change();
            $table->double('longitude')->nullable(false)->change();
            $table->double('nbr_of_samples')->nullable(false)->change();
            $table->double('nbr_of_samples_where_q_equal_1')->nullable(false)->change();
            $table->double('nbr_of_satellites')->nullable(false)->change();
        });
    }
}
