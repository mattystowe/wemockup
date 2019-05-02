<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Product;
use App\Sku;
use App\InputOption;
use App\Postproc;
use Ramsey\Uuid\Uuid;

class DemoProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      //////////////////////////////////////////////////////////////////////////////////////////
      /// Sample Product 1
      ///
      ///
      ///
      $product = new Product;
      $uuid4 = Uuid::uuid4();
      $product->productuid = $uuid4->toString();
      $product->name = 'Test Product 1 (Single Frame)';
      $product->category_id = 1; // general
      $product->type_id = 1; // single frame
      $product->description = 'Here is the product description.';
      $product->frame_start = 1;
      $product->frame_end = 1;
      $product->image = 'https://s3-eu-west-1.amazonaws.com/wemockup/productimages/5G7bJFaTwr0kteQe7vCQ_output_0001.png';
      $product->location = '1';
      $product->save();

      $sku1 = new Sku;
      $uuid4 = Uuid::uuid4();
      $sku1->skuuid = $uuid4->toString();
      $sku1->product_id = $product->id;
      $sku1->name = 'Free Sample';
      $sku1->description = 'Small 400x300';
      $sku1->frameconfig_id = 1; // Free Sample (400x300 Watermarked)
      $sku1->save();

      $sku2 = new Sku;
      $uuid4 = Uuid::uuid4();
      $sku2->skuuid = $uuid4->toString();
      $sku2->product_id = $product->id;
      $sku2->name = 'Medium (1152x864 4:3)';
      $sku2->description = 'Medium 1152x864 ideal for web.';
      $sku2->frameconfig_id = 3; // Free Sample (400x300 Watermarked)
      $sku2->save();


      $inputoption1 = new InputOption;
      $inputoption1->product_id = $product->id;
      $inputoption1->name = 'Main image';
      $inputoption1->description = 'Upload your main image that will be used to mockup your creation.';
      $inputoption1->input_type = 'imageupload';
      $inputoption1->variable_name = 'mainimage';
      $inputoption1->priority = 1;
      $inputoption1->data = '{
                              "imagecropratio": "9/16",
                              "imagedimmin": "1080,1920"
                            }';
      $inputoption1->image = 'https://s3-eu-west-1.amazonaws.com/wemockup/inputoptionimages/i3XHnPlSN6tS1NT1EhQW_InputOptionDemo1.png';
      $inputoption1->save();

      //
      //
      //
      //
      /////////////////////////////////////////////////////////////////////////////////////////////


      //////////////////////////////////////////////////////////////////////////////////////////
      /// Sample Product 2
      ///
      ///
      ///
      $product = new Product;
      $uuid4 = Uuid::uuid4();
      $product->productuid = $uuid4->toString();
      $product->name = 'Test Product 2 (MultiFrame Video)';
      $product->category_id = 1; // general
      $product->type_id = 2; // Multi frame
      $product->description = 'Here is the product description.';
      $product->frame_start = 1;
      $product->frame_end = 160;
      $product->image = 'https://s3-eu-west-1.amazonaws.com/wemockup/productimages/5G7bJFaTwr0kteQe7vCQ_output_0001.png';
      $product->location = '2';
      $product->save();

      $sku1 = new Sku;
      $uuid4 = Uuid::uuid4();
      $sku1->skuuid = $uuid4->toString();
      $sku1->product_id = $product->id;
      $sku1->name = 'Large Video';
      $sku1->description = 'MOV and MPEG Output at 720p (1280 x 720)';
      $sku1->frameconfig_id = 7; // Free Sample (400x300 Watermarked)
      $sku1->save();

      $postproc = Postproc::find(1); // ExportMOV
      $sku1->postprocs()->attach($postproc->id, ['priority'=>1]);




      $inputoption1 = new InputOption;
      $inputoption1->product_id = $product->id;
      $inputoption1->name = 'Main image';
      $inputoption1->description = 'Upload your main image that will be used to mockup your creation.';
      $inputoption1->input_type = 'videoupload';
      $inputoption1->variable_name = 'mainimage';
      $inputoption1->priority = 1;
      $inputoption1->data = '{
                              "imagecropratio": "9/16",
                              "imagedimmin": "720,1280"
                            }';
      $inputoption1->image = 'https://s3-eu-west-1.amazonaws.com/wemockup/inputoptionimages/i3XHnPlSN6tS1NT1EhQW_InputOptionDemo1.png';
      $inputoption1->save();

      //
      //
      //
      //
      /////////////////////////////////////////////////////////////////////////////////////////////


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
