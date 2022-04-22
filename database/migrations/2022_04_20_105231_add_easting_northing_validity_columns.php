<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

use App\Console\Commands\FetchDeviceDataFtp;

use App\Models\ConfigurationBaseStation;
use App\Models\Device;
use App\Models\DeviceBaseStation;
use App\Models\DeviceRover;
use App\Models\File;
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
                $table->double('absolute_easting');
                $table->double('absolute_northing');
                $table->double('absolute_up');
                $table->double('relative_easting');
                $table->double('relative_northing');
                $table->double('relative_up');
            });
        });

        // Fill the columns based on the current values
        $positions = Position::get();
        foreach ($positions as $position) {
            // Get the configurations corresponding to that base station
            $configurations = ConfigurationBaseStation::where([['device_base_station_id', DeviceBaseStation::where('id', DeviceRover::where('id', $position->device_rover_id)->first()->device_base_station_id)->first()->id], ['reference_latitude', '<>', NULL], ['reference_longitude', '<>', NULL]])->get();
            
            // Take the correct reference latitude/longitude/altitude
            $positionDateTime = File::where('id', $position->file_id)->first()->upload_time;
            $reference_configuration = $configurations[0];
            $reference_rover = Position::where('device_rover_id', $position->device_rover_id)->first();
            foreach($configurations as $configuration)
            {
                $configurationDateTime = File::where('id', $configuration->file_id)->first()->upload_time;
                if ($configurationDateTime <= $positionDateTime)
                {
                    $reference_configuration = $configuration;

                    // Find the first positon reported after that this configuration was applied.
                    $reference_rover = Position::join('files', 'files.id', '=', 'positions.file_id')
                                                ->where([['device_rover_id', $position->device_rover_id],
                                                         ['upload_time', '>=', $configurationDateTime],
                                                         ['type', 'pos']])
                                                ->select('positions.latitude', 'positions.longitude', 'positions.height')
                                                ->first();
                }
            }

            // Calculate the easting - northing - up
            $f = new FetchDeviceDataFtp;
            $v0_installation = $f->fromECEFgToECEFr($reference_configuration->reference_latitude, $reference_configuration->reference_longitude, $reference_configuration->reference_altitude);
            $v_rover = $f->fromECEFgToECEFr($reference_rover->latitude, $reference_rover->longitude, $reference_rover->height);
            $v = $f->fromECEFgToECEFr($position->latitude, $position->longitude, $position->height);
            $enu_installation = $f->fromECEFrtoLTP($v0_installation, $v);
            $enu_rover = $f->fromECEFrtoLTP($v0_installation, $v_rover);

            // Update position line with new values
            Position::updateOrInsert(
                ['id' => $position->id],
                [
                    'absolute_easting' => $enu_installation["e"],
                    'absolute_northing' => $enu_installation["n"],
                    'absolute_up' => $enu_installation["u"],
                    'relative_easting' => $enu_installation["e"] - $enu_rover["e"],
                    'relative_northing' => $enu_installation["n"] - $enu_rover["n"],
                    'relative_up' => $enu_installation["u"] - $enu_rover["u"]
                ]
            );

            // 
        }
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
