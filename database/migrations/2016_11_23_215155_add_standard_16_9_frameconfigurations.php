<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStandard169Frameconfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      /////////////////////
      ///
      ///16:9 format
      DB::table('frameconfigs')->insert(
        array(
            'name' => 'Small (400x255 16:9)',
            'dimx' => '400',
            'dimy' => '255',
            'outputformat' => 'PNG',
            'watermark' => false
        )
      );
      DB::table('frameconfigs')->insert(
        array(
            'name' => 'Medium (1152x648 16:9)',
            'dimx' => '1152',
            'dimy' => '648',
            'outputformat' => 'PNG',
            'watermark' => false
        )
      );
      DB::table('frameconfigs')->insert(
        array(
            'name' => 'Large (1920x1080 16:9)',
            'dimx' => '1920',
            'dimy' => '1080',
            'outputformat' => 'PNG',
            'watermark' => false
        )
      );
      DB::table('frameconfigs')->insert(
        array(
            'name' => 'Extra Large (3300x1856 16:9)',
            'dimx' => '3300',
            'dimy' => '1856',
            'outputformat' => 'PNG',
            'watermark' => false
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
      DB::table('frameconfigs')->where('name', '=', 'Small (400x255 16:9)')->delete();
      DB::table('frameconfigs')->where('name', '=', 'Medium (1152x648 16:9)')->delete();
      DB::table('frameconfigs')->where('name', '=', 'Large (1920x1080 16:9)')->delete();
      DB::table('frameconfigs')->where('name', '=', 'Extra Large (3300x1856 16:9)')->delete();
    }
}
