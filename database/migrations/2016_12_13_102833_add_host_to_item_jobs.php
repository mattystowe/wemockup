<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHostToItemJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('item_jobs', function (Blueprint $table) {
        $table->string('instance_type',255)->nullable();
        $table->string('hostname',255)->nullable();
      });
      Schema::table('itempostprocs', function (Blueprint $table) {
        $table->string('instance_type',255)->nullable();
        $table->string('hostname',255)->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('item_jobs', function (Blueprint $table) {
        $table->dropColumn('instance_type');
      });
      Schema::table('item_jobs', function (Blueprint $table) {
        $table->dropColumn('hostname');
      });
      Schema::table('itempostprocs', function (Blueprint $table) {
        $table->dropColumn('instance_type');
      });
      Schema::table('itempostprocs', function (Blueprint $table) {
        $table->dropColumn('hostname');
      });
    }
}
