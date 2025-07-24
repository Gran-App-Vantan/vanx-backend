<?php

use App\Models\Booth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\BoothController;
use App\Http\Controllers\Api\GameController;


Route::prefix('account')->group(function () {
    Route::post('/sign-up', [AccountController::class, 'register']);
    Route::post('/login', [AccountController::class, 'login']);
});

Route::prefix('post')->group(function () {
    Route::get('/posts', [PostController::class,'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/token', function() {
        return response()->json([
            'success' => true,
        ]);
    });
    Route::prefix('account')->group(function () {
        Route::put('/update', [AccountController::class,'update']);
        Route::prefix('wallet')->group(function () {
            Route::get('/get', [AccountController::class,'wallet']);
            Route::put('/update', [AccountController::class,'wallet_update']);
        });
        Route::get('/ranking', [AccountController::class,'ranking']);
        Route::get('/profile/{id}', [AccountController::class,'profile']);
    });
    
    Route::prefix('post')->group(function () {
            Route::get('/post/{id}', [PostController::class,'show']);
            Route::post('/post', [PostController::class,'store']);
            Route::delete('/post/{id}', [PostController::class,'delete']);
            
            Route::prefix('reaction_ops')->group(function () {
                Route::delete('/{id}', [PostController::class,'reaction_ops']);
            });
    });

});

Route::get('floor_map', [BoothController::class,'floor_map']);
Route::get('rule/{id}', [GameController::class,'game_rule']);