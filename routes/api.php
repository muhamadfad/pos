<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PosSyncController;
use App\Http\Controllers\Api\CustomerSyncController;

Route::post('/pos/sync', [PosSyncController::class, 'store'])->name('api.pos.sync');
Route::post('/customers/sync', [CustomerSyncController::class, 'store'])->name('api.customers.sync');
