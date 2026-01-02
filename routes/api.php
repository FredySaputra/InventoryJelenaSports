<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::post('/users/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/users/logout', [UserController::class, 'logout']);
    Route::apiResource('pelanggans', \App\Http\Controllers\Api\PelanggansController::class);

//    Route::get('/users', function (Request $request) {
//        return $request->user();
//    });
});
