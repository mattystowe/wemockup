<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'IndexController@index');

Route::get('/testproductfiles', 'TestController@testProductFiles');
Route::get('/test', 'TestController@test');



//main entry point for users accessing their orders from email link
Route::get('/order/', 'OrdersController@index');




//Receive new order webhook from shopify
Route::post('/shopify/neworder/{apikey}', 'ShopifyController@neworder');



//Order and Item API
Route::get('/orders/{orderuid}', 'OrdersController@loadOrder');
Route::get('/orders/items/{itemuid}', 'OrdersController@loadItem');
Route::get('/orders/itemswithstats/{itemuid}', 'OrdersController@loadItemWithStats');
Route::get('/orders/itemjobs/{itemuid}', 'OrdersController@loadItemJobs')->middleware('auth');
Route::get('/orders/itempostprocs/{itemuid}', 'OrdersController@loadItemPostprocs')->middleware('auth');
Route::get('/orders/itemjoblog/{itemjobid}', 'OrdersController@loadItemJobLog')->middleware('auth');
Route::get('/orders/itempostproclog/{itempostprocid}', 'OrdersController@loadItemPostProcLog')->middleware('auth');
Route::post('/orders/items/{itemuid}/submitforprocessing', 'OrdersController@submitItem');
Route::post('/orders/items/{itemuid}/resubmitforprocessing', 'OrdersController@resubmitItem');
Route::post('/orders/items/{itemuid}/cancel', 'OrdersController@cancelItem')->middleware('auth');
Route::get('/ordersearch/{query?}','OrdersController@search')->middleware('auth');
Route::post('/orders/createtestorder/{skuid}','OrdersController@createTestOrder')->middleware('auth');

//items
Route::get('/items/processing','ItemsController@getProcessing');





//Administration Portal
Route::auth();
Route::get('/administrator', 'AdminController@index');

//API Categories
Route::get('/categories', 'CategoriesController@getall');
Route::get('/categories/destroy/{categoryid}', 'CategoriesController@destroy');
Route::post('/categories', 'CategoriesController@add');
Route::post('/categories/edit', 'CategoriesController@edit');


//API Product Types
Route::get('/types', 'TypesController@getall');
Route::get('/types/destroy/{typeid}', 'TypesController@destroy');
Route::post('/types', 'TypesController@add');
Route::post('/types/edit', 'TypesController@edit');


//API Frame Configurations
Route::get('/frameconfigurations', 'FrameConfigurationsController@getall');
Route::post('/frameconfigurations', 'FrameConfigurationsController@add');
Route::post('/frameconfigurations/edit', 'FrameConfigurationsController@edit');
Route::get('/frameconfigurations/destroy/{frameconfigid}', 'FrameConfigurationsController@destroy');

//API Post Processing Steps
Route::get('/postprocessing', 'PostprocessingController@getall');
Route::post('/postprocessing', 'PostprocessingController@add');
Route::post('/postprocessing/edit', 'PostprocessingController@edit');
Route::get('/postprocessing/destroy/{stepid}', 'PostprocessingController@destroy');



//API Products
Route::post('/products', 'ProductsController@add');
Route::post('/products/{productid}/edit', 'ProductsController@edit');
Route::post('/products/{productid}/destroy', 'ProductsController@destroy');
Route::get('/products', 'ProductsController@getall');
Route::get('/products/{productid}', 'ProductsController@load');
Route::post('/products/{productid}/inputoptions', 'ProductsController@addInputOption');
Route::post('/products/inputoptions/{inputoptionid}/edit', 'ProductsController@editInputOption');
Route::post('/products/inputoptions/{inputoptionid}/destroy', 'ProductsController@deleteInputOption');
Route::post('/products/{productid}/inputoptions/order', 'ProductsController@orderInputOptions');


//API Sku Management
Route::post('/products/sku', 'ProductsController@addSku');
Route::post('/products/sku/editsku', 'ProductsController@editSku');
Route::get('/products/sku/{skuid}', 'ProductsController@loadSku');
Route::post('/products/sku/{skuid}/destroy', 'ProductsController@deleteSku');

Route::post('/skus/{skuid}/postprocs', 'SkusController@addPostProc'); // associate post proc with sku
Route::post('/skus/{skuid}/postprocs/remove', 'SkusController@removePostProc'); // remove post proc with sku
Route::post('/skus/{skuid}/postprocs/order', 'SkusController@orderPostProcs'); // save ordering of post procs for sku

//Host Worker api
Route::get('/hosts/healthy','HostsController@getHealthyHosts');


//Scheduled Tasks
Route::get('/scheduledtasks/sendpendingitemreminders','ScheduledTasksController@sendPendingItemReminderEmails');
Route::get('/scheduledtasks/schedulerun','ScheduledTasksController@scheduleRun');


//Renderstreet
Route::get('/renderstreet/result/{itemjobid}','RenderStreetController@result');



//Private API routes
Route::group(['prefix' => 'api/v1','middleware'=>'apikey'], function () {

  //Route::get('test','Api\ApiController@test');
  Route::post('/order/new','Api\ApiController@newOrder');
  Route::get('/sku/get/{skuid}','Api\ApiController@getSku');
  Route::get('/product/search/{query}','Api\ApiController@searchProducts');
  Route::get('/product/get/{productid}','Api\ApiController@getProduct');
  Route::get('/item/get/{itemid}','Api\ApiController@getItem');
  Route::post('/items/get','Api\ApiController@getItems');

});
