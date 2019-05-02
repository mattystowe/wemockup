<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostprocForRenderstSinglefileOutput extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::table('postprocs')->insert(
            array(
                'name' => 'Files: Copy Render.st to output',
                'jobname' => 'FilesRenderstToOutput',
                'data' => '{}'
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
          ->where('jobname', '=','FilesRenderstToOutput')
          ->delete();
    }
}
