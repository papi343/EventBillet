<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});

Route::prefix("v1")->group(function(){
    Route::post('/register',[AuthController::class,'register'])->middleware('throttle:register');
    Route::post('/login',[AuthController::class,'login'])->middleware('throttle:login');
});
