<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DemoApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/products', [DemoApiController::class, 'storeProduct']);
Route::get('/products/{id}', [DemoApiController::class, 'showProduct']);
Route::post('/orders', [DemoApiController::class, 'storeOrder']);
Route::get('/profile', [DemoApiController::class, 'profile']);
Route::get('/system-check', [DemoApiController::class, 'systemCheck']);