<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Position;

class ConvertEastingNorthingUpFromMeterToCentimeter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            Position::chunk(100, function($positions)
            {
                foreach ($positions as $position) 
                {
                    // Conversion from meter to centimeter
                    $position->absolute_easting *= 100;
                    $position->absolute_northing *= 100;
                    $position->absolute_up *= 100;
                    $position->relative_easting *= 100;
                    $position->relative_northing *= 100;
                    $position->relative_up *= 100;
                    $position->update();
                }
            });
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
            Position::chunk(100, function($positions)
            {
                foreach ($positions as $position) 
                {
                    // Conversion from centimeter to meter
                    $position->absolute_easting /= 100;
                    $position->absolute_northing /= 100;
                    $position->absolute_up /= 100;
                    $position->relative_easting /= 100;
                    $position->relative_northing /= 100;
                    $position->relative_up /= 100;
                    $position->update();
                }
            });
        });
    }
}
