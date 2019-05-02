<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostprocsSkus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('postproc_sku', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('sku_id');
        $table->integer('postproc_id');
        $table->integer('priority');
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
      Schema::drop('postproc_sku');
    }
}
