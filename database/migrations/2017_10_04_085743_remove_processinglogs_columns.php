<?php
//Remove the processinglog colum from itemjobs and itempostprocs tables.
//
//
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveProcessinglogsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('item_jobs', function (Blueprint $table) {
        $table->dropColumn('processinglog');
      });
      Schema::table('itempostprocs', function (Blueprint $table) {
        $table->dropColumn('processinglog');
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
        $table->mediumText('processinglog')->nullable();
      });
      Schema::table('itempostprocs', function (Blueprint $table) {
        $table->mediumText('processinglog')->nullable();
      });
    }
}
