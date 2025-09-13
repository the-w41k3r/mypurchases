<?php

use Azuriom\Plugin\MyPurchases\Controllers\MyPurchasesHomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MyPurchasesHomeController::class, 'index'])->name('index');
