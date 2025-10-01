<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\spotController;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/register',[AuthController::class, 'register']);

    Route::post('/login',[AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/spots',spotController::class);

    Route::apiResource('review', ReviewController::class)
    ->only([
        'store',
        'destroy'
    ])
    ->middlewareFor(['store'], 'ensureUserHasRole:user')
    ->middlewareFor(['destroy'], 'ensureUserHasRole:ADMIN');

});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
