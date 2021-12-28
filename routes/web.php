<?php

use App\Http\Controllers\SellerController;
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

Route::get('seller/{id}', [SellerController::class, 'show'])->name('seller.profile');
Route::get('stripe/{id}', [SellerController::class, 'redirectToStripe'])->name('redirect.stripe');
Route::get('connect/{token}', [SellerController::class, 'saveStripeAccount'])->name('save.stripe');
Route::post('charge/{id}', [SellerController::class, 'purchase'])->name('complete.purchase');

Route::get('checkout/{product}', [SellerController::class, 'checkout'])->name('checkout');
Route::post('token', [SellerController::class, 'createToken'])->name('create.token');
