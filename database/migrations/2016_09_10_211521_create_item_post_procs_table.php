<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemPostProcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itempostprocs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->integer('postproc_id');
            $table->integer('priority');
            $table->string('status');
            $table->timestamp('date_queued')->nullable();
            $table->timestamp('date_processing')->nullable();
            $table->timestamp('date_complete')->nullable();
            $table->timestamp('date_failed')->nullable();
            $table->timestamp('date_aborted')->nullable();
            $table->mediumText('processinglog')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('itempostprocs');
    }
}
