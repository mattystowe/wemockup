<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePostprocData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::table('postprocs')
          ->where('jobname', '=','ExportMOV')
          ->update(['data' => '{
            "type":"normal",
            "outputframerate":"30",
            "inputframerate":"30"
          }']);
      DB::table('postprocs')->insert(
            array(
                'name' => 'Export MOV H.264 Slideshow',
                'jobname' => 'ExportMOVSlideshow',
                'data' => '{
                  "type":"slideshow",
                  "outputframerate":"30",
                  "inputframerate":"1"
                  }'
            )
      );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::table('postprocs')
          ->where('name', '=','Export MOV H.264')
          ->update(['data' => '{}']);
      DB::table('postprocs')
          ->where('jobname', '=','ExportMOVSlideshow')
          ->delete();
    }
}
