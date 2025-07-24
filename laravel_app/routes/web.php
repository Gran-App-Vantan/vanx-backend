<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestControllers\TopController;



Route::prefix('test')->group(function () {
    Route::prefix('accounts')->group(function () {
        Route::prefix('register')->group(function () {
            Route::get('/', [TopController::class,'register'])->name('register');
            Route::post('/', [TopController::class,'register_store'])->name('register.store');
        });
        Route::prefix('login')->group(function () {
            Route::get('/', [TopController::class,'login'])->name('login');
            Route::post('/', [TopController::class,'login_store'])->name('login.store');
        });
        Route::prefix('edit')->group(function () {
            Route::get('/{id}', [TopController::class,'edit'])->name('edit')->middleware('auth');
            Route::put('/{id}', [TopController::class,'update'])->name('edit.update')->middleware('auth');
        });
        Route::get('/profile/{id}', [TopController::class,'profile'])->name('profile')->middleware('auth');
        Route::get('/wallet', [TopController::class,'wallet'])->name('wallet')->middleware('auth');
        Route::get('/ranking', [TopController::class,'ranking'])->name('ranking')->middleware('auth');
    });
    Route::get('/top', [TopController::class, 'index'])->name('test.top');
    Route::get('/posts', [TopController::class, 'show_posts'])->name('post.show')->middleware('auth');
    Route::prefix('/reaction')->group(function () {
        Route::get('/{id}', [TopController::class,'reaction'])->name('reaction')->middleware('auth');
        Route::post('/{id}', [TopController::class,'reaction_store'])->name('reaction.store')->middleware('auth');
        Route::delete('/{post}/reaction/{reaction}', [TopController::class, 'reaction_delete'])->name('reaction.delete')->middleware('auth');
    });
    Route::prefix('/post')->group(function () {
        Route::get('/', [TopController::class,'post_create'])->name('post.create')->middleware('auth');
        Route::post('/', [TopController::class,'post_store'])->name('post.store')->middleware('auth');
        Route::delete('/{id}', [TopController::class,'post_delete'])->name('post.delete')->middleware('auth');
    });
    Route::get('/floor_map/{id}', [TopController::class,'floor_map'])->name('floor_map');
    Route::get('/rule/{id}', [TopController::class,'game_rule'])->name('game_rule');

});


Route::get('/', function () {
    return view('test.top');
});