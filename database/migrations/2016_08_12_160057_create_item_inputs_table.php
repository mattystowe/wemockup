<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_inputs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->string('input_type',255);
            $table->string('variable_name',255);
            $table->text('value');
            $table->text('filename')->nullable();
            $table->text('filekey')->nullable();
            $table->text('filestackurl')->nullable();
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
        Schema::drop('item_inputs');
    }
}
