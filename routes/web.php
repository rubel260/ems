<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', 'App\Http\Controllers\ProductController@home');

// Route::get('/products', function () {
//     return view('products');
// });

Route::get('/product_details', function () {
    return view('product_details');
});

Route::get('/account', function () {
    return view('account');
})->name('account');

// Route::get('/cart', function () {
//     return view('cart');
// });

Route::resource('/products', ProductController::class);

Route::resource('/users', UserController::class);

Route::get('/admin_products', 'App\Http\Controllers\ProductController@addProduct' )->middleware('auth');

Route::post('add-to-cart', 'App\Http\Controllers\ProductController@addToCart');

Route::get('/cart','App\Http\Controllers\ProductController@viewCart');
Route::get('/remove-item/{rowId}', '\App\Http\Controllers\ProductController@removeItem');

Route::post('/products/validate-amount', '\App\Http\Controllers\ProductController@validateAmount');

Route::get('/example1', [\App\Http\Controllers\SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [\App\Http\Controllers\SslCommerzPaymentController::class, 'exampleHostedCheckout']);

Route::post('/pay', [\App\Http\Controllers\SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [\App\Http\Controllers\SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/success', [\App\Http\Controllers\SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [\App\Http\Controllers\SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [\App\Http\Controllers\SslCommerzPaymentController::class, 'cancel']);

Route::post('/ipn', [\App\Http\Controllers\SslCommerzPaymentController::class, 'ipn']);