<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('jobname');
            $table->timestamps();
        });
        DB::table('types')->insert(
          array(
              'name' => 'Single Frame',
              'jobname' => 'BlenderSingleFrame'
          )
        );
        DB::table('types')->insert(
          array(
              'name' => 'Multiple Frames',
              'jobname' => 'BlenderMultipleFrame'
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
        Schema::drop('types');
    }
}
