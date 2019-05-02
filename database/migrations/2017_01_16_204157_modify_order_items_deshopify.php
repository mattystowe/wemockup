<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyOrderItemsDeshopify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('items', function ($table) {
        $table->dropColumn('shopify_line_item_id');

      });
      Schema::table('items', function ($table) {

        $table->dropColumn('shopify_line_item_variant_id');


      });
      Schema::table('items', function ($table) {

        $table->dropColumn('shopify_line_item_variant_title');


      });
      Schema::table('items', function ($table) {

        $table->dropColumn('shopify_line_item_title');


      });
      Schema::table('items', function ($table) {

        $table->dropColumn('shopify_line_item_product_id');

      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('items', function ($table) {
        $table->string('shopify_line_item_id',255)->nullable();
        $table->string('shopify_line_item_variant_id',255)->nullable();
        $table->text('shopify_line_item_variant_title',255)->nullable();
        $table->text('shopify_line_item_title',255)->nullable();
        $table->string('shopify_line_item_product_id',255)->nullable();
      });
    }
}
