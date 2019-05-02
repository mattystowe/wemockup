<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('applications', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamps();
          $table->string('name');
          $table->string('api_key')->unique()->index();
          $table->string('email');
      });

      //Doohpress
      $uuid4 = Uuid::uuid4();
      $now = Carbon::now()->toDateTimeString();
      DB::table('applications')->insert(
        array(
            'created_at'=>$now,
            'updated_at'=>$now,
            'name' => 'doohpress',
            'api_key' => 'b0d4ee07-a0d2-44c6-bb22-8bf82de8eea8',
            'email'=>'info@doohpress.com'
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
        Schema::drop('applications');
    }
}
