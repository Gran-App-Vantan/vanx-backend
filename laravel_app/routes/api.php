<?php

use App\Models\Booth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\BoothController;
use App\Http\Controllers\Api\GameController;



Route::prefix('account')->group(function () {
    Route::post('/sign-up', [AccountController::class, 'register']);
    Route::post('/login', [AccountController::class, 'login']);
    Route::get('/show/{user_id}', [AccountController::class,'show']);

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
        Route::get('me', [AccountController::class,'me']);
        Route::post('/update', [AccountController::class,'update']);
        Route::prefix('wallet')->group(function () {
            Route::get('/get', [AccountController::class,'wallet']);
        });
        Route::get('/ranking', [AccountController::class,'ranking']);
        Route::get('/profile/{id}', [AccountController::class,'profile']);
    });
    
    Route::prefix('post')->group(function () {
            Route::get('/focus/{id}', [PostController::class,'show']);
            Route::post('/post', [PostController::class,'store']);
            Route::delete('/delete/{id}', [PostController::class,'delete']);
            
            Route::prefix('reaction')->group(function () {
                Route::get('/get', [ReactionController::class,'reactions']);
                Route::post('/{post_id}', [ReactionController::class,'reaction']);
            });
    });
    
});
Route::middleware(['auth:sanctum', 'can:is-dealer'])->group(function () {
    Route::prefix('game')->group(function () {
        Route::post('/create-url', [GameController::class,'create_url']);
        Route::post('/token-check', [GameController::class,'token_check']);
        Route::delete('/token-delete', [GameController::class,'token_delete']);
    });
    Route::prefix('account')->group(function () {
        Route::prefix('wallet')->group(function () {
            Route::patch('/update/{sns_id}', [AccountController::class,'wallet_update']);
        });
    });
});

Route::get('floor_map', [BoothController::class,'floor_map']);
Route::get('rule/{id}', [GameController::class,'game_rule']);




Route::get('/storage/{path}', function ($path) {
    // パスの正規化
    $normalizedPath = ltrim($path, '/');
    
    $fullPath = storage_path('app/public/' . $normalizedPath);
    
    if (!file_exists($fullPath)) {
        return response()->json(['error' => 'File not found'], 404);
    }
    
    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
    $contentType = match(strtolower($extension)) {
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'mp4' => 'video/mp4',
        'mov' => 'video/quicktime',
        'webm' => 'video/webm',
        default => 'application/octet-stream'
    };

    return response()->file($fullPath, [
        'Content-Type' => $contentType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');