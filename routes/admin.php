<?php

use Azuriom\Plugin\MyPurchases\Controllers\Admin\MyPurchasesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MyPurchasesController::class, 'index'])->name('index');
Route::post('/save', [MyPurchasesController::class, 'save'])->name('save');
