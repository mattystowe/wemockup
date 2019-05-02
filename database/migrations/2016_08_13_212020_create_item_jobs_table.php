<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->string('status',255); // QUEUED, PROCESSING, COMPLETE, ERROR
            $table->integer('progress')->nullable();
            $table->integer('frame'); // Frame number if applicable
            $table->text('data')->nullable(); // generic data in json format if needed
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
        Schema::drop('item_jobs');
    }
}
