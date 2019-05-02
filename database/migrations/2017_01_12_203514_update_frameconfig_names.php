<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFrameconfigNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::table('frameconfigs')
          ->where('name', '=','Small (400x300 4:3)')
          ->update(['name' => '4:3 Small (400x300)']);

      DB::table('frameconfigs')
          ->where('name', '=','Medium (1152x864 4:3)')
          ->update(['name' => '4:3 Medium (1152x864)']);

      DB::table('frameconfigs')
          ->where('name', '=','Large (1920x1440 4:3)')
          ->update(['name' => '4:3 Large (1920x1440)']);

      DB::table('frameconfigs')
          ->where('name', '=','Extra Large (3300x2475 4:3)')
          ->update(['name' => '4:3 Extra Large (3300x2475)']);

      DB::table('frameconfigs')
          ->where('name', '=','Small (400x255 16:9)')
          ->update(['name' => '16:9 Small (400x255)']);

      DB::table('frameconfigs')
          ->where('name', '=','Medium (1152x648 16:9)')
          ->update(['name' => '16:9 Medium (1152x648)']);

      DB::table('frameconfigs')
          ->where('name', '=','Large (1920x1080 16:9)')
          ->update(['name' => '16:9 Large (1920x1080)']);

      DB::table('frameconfigs')
          ->where('name', '=','Extra Large (3300x1856 16:9)')
          ->update(['name' => '16:9 Extra Large (3300x1856)']);


      DB::table('frameconfigs')
          ->where('name', '=','Standard Video (640 x 360)')
          ->update(['name' => 'Video 16:9 Small (640x360)']);

      DB::table('frameconfigs')
          ->where('name', '=','Large Video 720p (1280 x 720)')
          ->update(['name' => 'Video 16:9 Standard 720p (1280x720)']);

      DB::table('frameconfigs')
          ->where('name', '=','HD Video 1080p (1920 x 1080)')
          ->update(['name' => 'Video 16:9 Large HD 1080p (1920x1080)']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
