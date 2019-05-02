<?php
/**
 * Post Processing Stages - Definitions available for joining to SKUs
 *
 * eg output stitch MOV, MPEG,
 *
 *
 *
 *
 */
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostprocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postprocs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->string('jobname');
            $table->text('data')->nullable();
            $table->timestamps();
        });
        DB::table('postprocs')->insert(
          array(
              'name' => 'Export MOV H.264',
              'jobname' => 'ExportMOV',
              'data' => '{}'
          )
        );
        DB::table('postprocs')->insert(
          array(
              'name' => 'Files: Copy Working To Output',
              'jobname' => 'FilesCopyWorkingToOutput',
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
        Schema::drop('postprocs');
    }
}
