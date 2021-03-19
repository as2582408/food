<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ShopController@Index');

Route::get('/shop', 'ShopController@shopIndex');
Route::get('/shopContent/{id}', 'ShopController@shopContent');
Route::get('/editShop/{id}', 'ShopController@editShop');


Route::get('/addShop', 'ShopController@addShop');
Route::post('/add', 'ShopController@postAddShop');
Route::post('/addProducts', 'ShopController@addProducts');

Route::get('/newDetail/{id}', 'ShopController@newDetailPage');
Route::post('/newDetail', 'ShopController@newDetail');
Route::get('/detailEditPassword/{id}', 'ShopController@detailEditPassword');
//管理編輯訂單頁面
Route::post('/detailEditPage', 'ShopController@detailEditPage');
Route::get('/detailEditPage/{id}', 'ShopController@getDetailEditPage');
//編輯
Route::post('/detailEdit', 'ShopController@detailEdit');
Route::get('/del/{id}', 'ShopController@delOrder');

//管理員訂單查看
Route::get('/detailOrderAdmin/{id}', 'ShopController@detailOrderAdmin');
Route::get('/detailOrderUserAdmin/{id}', 'ShopController@detailOrderUserAdmin');
Route::get('/detailOrderAdminList/{id}', 'ShopController@detailOrderAdminList');

//管理員詳細訂單
Route::get('/editOrder/{id}', 'ShopController@editOrderPage');
//編輯使用者訂單
Route::post('/editOrder', 'ShopController@editOrder');
//歷史頁面
Route::get('/history', 'ShopController@historyPage');


//付款頁面
Route::get('/detailOrderPay/{id}', 'ShopController@orderPay');
Route::get('/detailOrderPayAll/{id}', 'ShopController@orderPay');

//訂購頁面
Route::get('/order/{id}/{shopId}', 'ShopController@orderPage');
Route::post('/order', 'ShopController@addOrder');
//顯示詳細頁面
Route::get('/detailOrder/{id}', 'ShopController@detailOrder');
Route::get('/detailOrderUser/{id}', 'ShopController@detailOrderUser');
Route::get('/allOrder/{id}', 'ShopController@allOrder');


Route::get('/getOrderStatus/{id}', 'ShopController@getOrderStatus');
Route::get('/changOrderStatus/{id}/{status}', 'ShopController@changOrderStatus');
Route::get('/allDetail', 'ShopController@allDetail');
Route::get('/hisAllDetail', 'ShopController@hisAllDetail');
Route::get('/allOrderAjax/{id}', 'ShopController@allOrderAjax');






