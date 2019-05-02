<?php
/**
 *
 * Generate 5 random orders
 *
 * Puts the new orders on the email queue - Process the emails queue to get the test emails.
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
use Illuminate\Database\Seeder;
use App\Order;
use App\Item;
use Ramsey\Uuid\Uuid;


class DemoOrders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // use the factory to create a Faker\Generator instance
      $faker = Faker\Factory::create();

      $orders = 5; // generate 5 demo orders

      for ($i=0; $i < $orders; $i++) {


          //create order
          $uuid4 = Uuid::uuid4();
          $orderattributes = array(
            'shopify_order_id' => $faker->randomNumber,
            'email' => $faker->email,
            'orderuid' => $uuid4->toString(),
            'amount' => 11.99
          );

          $order = Order::createNew($orderattributes);

          $uuid4 = Uuid::uuid4();

          $item = new Item;
          $item->itemuid = $uuid4->toString();
          $item->shopify_line_item_id = $faker->randomNumber;
          $item->shopify_line_item_variant_id = $faker->randomNumber;
          $item->shopify_line_item_variant_title = $faker->sentence(6,true);
          $item->shopify_line_item_title = $faker->sentence(6,true);
          $item->shopify_line_item_product_id = $faker->randomNumber;
          $item->skucode = 1;
          $item->sku_id = 1;
          $item->status = 'PENDINGSETUP';
          $order->items()->save($item);

          $uuid4 = Uuid::uuid4();

          $item = new Item;
          $item->itemuid = $uuid4->toString();
          $item->shopify_line_item_id = $faker->randomNumber;
          $item->shopify_line_item_variant_id = $faker->randomNumber;
          $item->shopify_line_item_variant_title = $faker->sentence(6,true);
          $item->shopify_line_item_title = $faker->sentence(6,true);
          $item->shopify_line_item_product_id = $faker->randomNumber;
          $item->skucode = 2;
          $item->sku_id = 2;
          $item->status = 'PENDINGSETUP';
          $order->items()->save($item);

          $uuid4 = Uuid::uuid4();

          $item = new Item;
          $item->itemuid = $uuid4->toString();
          $item->shopify_line_item_id = $faker->randomNumber;
          $item->shopify_line_item_variant_id = $faker->randomNumber;
          $item->shopify_line_item_variant_title = $faker->sentence(6,true);
          $item->shopify_line_item_title = $faker->sentence(6,true);
          $item->shopify_line_item_product_id = $faker->randomNumber;
          $item->skucode = 3;
          $item->sku_id = 3;
          $item->status = 'PENDINGSETUP';
          $order->items()->save($item);

      }


    }
}
