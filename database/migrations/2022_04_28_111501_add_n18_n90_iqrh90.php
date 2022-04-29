<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Console\Commands\FetchDeviceDataFtp;
use App\Models\Position;

class AddN18N90Iqrh90 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->after('nbr_of_samples_where_q_equal_1', function($table) {
                $table->bigInteger('n18')->nullable(true);
                $table->bigInteger('n90')->nullable(true);
                $table->double('iqrh90')->nullable(true);
            });
        });
        
        $f = new FetchDeviceDataFtp;
        $f->connect();
        Position::where('n18', NULL)->chunk(100, function($positions) use ($f)
        {
            foreach ($positions as $position) 
            {
                if ($position->iqrh90 == null)
                {
                    // Download the .pos file
                    $myFileRows = $f->getFile($position->file->path);

                    //Remove all header lines (useless)
                    foreach($myFileRows as $y => $myFileRow)
                    {
                        if ($myFileRow[0] == "%")
                        {
                            unset($myFileRows[$y]);
                        }
                    }

                    $myFileRows = array_values($myFileRows);
                    
                    // Check that there are position data in this file
                    if (count($myFileRows) == 0)
                    {
                        continue;
                    }

                    $n18_latitude = array();
                    $n18_longitude = array();
                    $n18_height = array();
                    $n90 = array();
                    for($i = 0; $i < count($myFileRows); $i++)
                    {
                        preg_match_all("([:\/\-\d.]+)", $myFileRows[$i], $matches);
                        $matches = $matches[0];

                        // Calculate n18 (position samples with Q = 1 in the last 18 position samples)
                        if (count($myFileRows) - $i <= 18)
                        {
                            if ($matches[5] == 1)
                            {
                                array_push($n18_latitude, $matches[2]);
                                array_push($n18_longitude, $matches[3]);
                                array_push($n18_height, $matches[4]);
                            }
                        }

                        // Calculate n90 (position samples with Q = 1 in the last 90 position samples)
                        if (count($myFileRows) - $i <= 90)
                        {
                            if ($matches[5] == 1)
                            {
                                array_push($n90, $matches[4]);
                            }
                        }
                    }

                    $position->n18 = count($n18_longitude);
                    $position->n90 = count($n90);
                    $position->iqrh90 = $f->getInterquartileRange($n90);
                    $position->update();
                }
            }

        });
        
        $f->disconnect();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('n18');
            $table->dropColumn('n90');
            $table->dropColumn('iqrh90');
        });
    }
}
