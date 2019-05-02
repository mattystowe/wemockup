<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRenderstreetJobtypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::table('types')->insert(
        array(
            'name' => 'Render.st Single Frame',
            'jobname' => 'RenderStSingleFrame'
        )
      );
      DB::table('types')->insert(
        array(
            'name' => 'Render.st Multiple Frame',
            'jobname' => 'RenderStMultipleFrame'
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
      DB::table('types')->where('jobname', '=', 'RenderStSingleFrame')->delete();
      DB::table('types')->where('jobname', '=', 'RenderStMultipleFrame')->delete();
    }
}
