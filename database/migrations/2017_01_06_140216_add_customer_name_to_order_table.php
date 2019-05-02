<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerNameToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('orders', function ($table) {
        $table->string('shopify_order_id',255)->nullable()->change();
        $table->string('email')->nullable()->change();
        $table->decimal('amount', 15, 2)->nullable()->change();
        $table->string('firstname',255)->nullable();
        $table->string('lastname',255)->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('orders', function ($table) {
        $table->string('shopify_order_id',255)->nullable(false)->change();
        $table->string('email')->nullable(false)->change();
        $table->decimal('amount', 15, 2)->nullable(false)->change();
      });
      Schema::table('orders', function ($table) {
        $table->dropColumn('firstname');
      });
      Schema::table('orders', function ($table) {
        $table->dropColumn('lastname');
      });
    }
}
