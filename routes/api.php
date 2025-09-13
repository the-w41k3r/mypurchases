<?php

use Azuriom\Plugin\MyPurchases\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;
use Azuriom\Plugin\MyPurchases\Controllers\MyPurchasesHomeController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', [ApiController::class, 'index']);
Route::get('/payment/{transactionId}', [MyPurchasesHomeController::class, 'getPaymentDetails']);
