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
        Route::post('/update', [AccountController::class,'update']);
        Route::prefix('wallet')->group(function () {
            Route::get('/get', [AccountController::class,'wallet']);
            Route::put('/update', [AccountController::class,'wallet_update']);
        });
        Route::get('/ranking', [AccountController::class,'ranking']);
        Route::get('/profile/{id}', [AccountController::class,'profile']);
    });
    
    Route::prefix('post')->group(function () {
            Route::get('/focus/{id}', [PostController::class,'show']);
            Route::post('/post', [PostController::class,'store']);
            Route::delete('/delete/{id}', [PostController::class,'delete']);
            
            Route::prefix('reaction')->group(function () {
                Route::delete('/{id}', [PostController::class,'reaction']);
            });
    });

});

Route::get('floor_map', [BoothController::class,'floor_map']);
Route::get('rule/{id}', [GameController::class,'game_rule']);

Route::get('/storage/post_files/{filename}', function ($filename) {
    $path = storage_path('app/public/post_files/' . $filename);
    
    if (!file_exists($path)) {
        return response()->json(['error' => 'File not found'], 404);
    }
    
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $contentType = match(strtolower($extension)) {
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'mp4' => 'video/mp4',
        'mov' => 'video/quicktime',
        default => 'application/octet-stream'
    };

    return response()->file($path, [
        'Content-Type' => $contentType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
});