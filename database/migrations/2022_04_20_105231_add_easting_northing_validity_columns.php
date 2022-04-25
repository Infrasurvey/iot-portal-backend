<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

use App\Console\Commands\FetchDeviceDataFtp;
use App\Models\Position;

class AddEastingNorthingValidityColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->after('longitude', function($table) {
                $table->double('absolute_easting')->nullable();
                $table->double('absolute_northing')->nullable();
                $table->double('absolute_up')->nullable();
                $table->double('relative_easting')->nullable();
                $table->double('relative_northing')->nullable();
                $table->double('relative_up')->nullable();
            });
        });

        // Fill the columns based on the current values
        Position::chunk(10, function($positions)
        {
            foreach ($positions as $position) 
            {
                // Calculate the easting - northing - up
                $f = new FetchDeviceDataFtp;
                $position = $f->calculateAbsoluteRelativeENU($position);
                $position->update();
            }
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
            $table->dropColumn('absolute_easting');
            $table->dropColumn('absolute_northing');
            $table->dropColumn('absolute_up');
            $table->dropColumn('relative_easting');
            $table->dropColumn('relative_northing');
            $table->dropColumn('relative_up');
        });
    }
}
