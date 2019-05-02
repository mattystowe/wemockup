<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFrameconfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frameconfigs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('dimx');
            $table->integer('dimy');
            $table->string('outputformat');
            $table->boolean('watermark');
            $table->timestamps();
        });

        /////////////////////
        ///
        ///IMAGES
        DB::table('frameconfigs')->insert(
          array(
              'name' => 'Free Sample (400x300)',
              'dimx' => '400',
              'dimy' => '300',
              'outputformat' => 'PNG',
              'watermark' => false
          )
        );
        DB::table('frameconfigs')->insert(
          array(
              'name' => 'Small (400x300 4:3)',
              'dimx' => '400',
              'dimy' => '300',
              'outputformat' => 'PNG',
              'watermark' => false
          )
        );
        DB::table('frameconfigs')->insert(
          array(
              'name' => 'Medium (1152x864 4:3)',
              'dimx' => '1152',
              'dimy' => '864',
              'outputformat' => 'PNG',
              'watermark' => false
          )
        );
        DB::table('frameconfigs')->insert(
          array(
              'name' => 'Large (1920x1440 4:3)',
              'dimx' => '1920',
              'dimy' => '1440',
              'outputformat' => 'PNG',
              'watermark' => false
          )
        );
        DB::table('frameconfigs')->insert(
          array(
              'name' => 'Extra Large (3300x2475 4:3)',
              'dimx' => '3300',
              'dimy' => '2475',
              'outputformat' => 'PNG',
              'watermark' => false
          )
        );

        ////////////////////////////
        ///
        ///VIDEOS
        DB::table('frameconfigs')->insert(
          array(
              'name' => 'Standard Video (640 x 360)',
              'dimx' => '640',
              'dimy' => '360',
              'outputformat' => 'PNG',
              'watermark' => false
          )
        );
        DB::table('frameconfigs')->insert(
          array(
              'name' => 'Large Video 720p (1280 x 720)',
              'dimx' => '1280',
              'dimy' => '720',
              'outputformat' => 'PNG',
              'watermark' => false
          )
        );
        DB::table('frameconfigs')->insert(
          array(
              'name' => 'HD Video 1080p (1920 x 1080)',
              'dimx' => '1920',
              'dimy' => '1080',
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
        Schema::drop('frameconfigs');
    }
}
