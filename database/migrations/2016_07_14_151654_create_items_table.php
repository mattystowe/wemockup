<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->string('itemuid', 255)->unique()->index();
            $table->string('shopify_line_item_id',255)->nullable();
            $table->string('shopify_line_item_variant_id',255)->nullable();
            $table->text('shopify_line_item_variant_title',255)->nullable();
            $table->text('shopify_line_item_title',255)->nullable();
            $table->string('shopify_line_item_product_id',255)->nullable();
            $table->string('skucode',255);
            $table->integer('sku_id');
            $table->decimal('price',15,2)->nullable();
            $table->string('status',255);
            $table->integer('progress')->nullable();
            $table->timestamp('date_queued')->nullable();
            $table->timestamp('date_processing')->nullable();
            $table->timestamp('date_finishing')->nullable();
            $table->timestamp('date_complete')->nullable();
            $table->timestamp('date_failed')->nullable();
            $table->timestamp('date_cancelled')->nullable();
            $table->text('cancelled_reason')->nullable();
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
        Schema::drop('items');
    }
}
