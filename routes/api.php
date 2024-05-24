<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('register', [AuthController::class, 'register']);
            Route::post('login', [AuthController::class, 'login']);
        });

        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::get('/users', [UserController::class, 'index']);
                Route::get('/users/{user}', [UserController::class, 'show']);

                Route::get('/chats', [ChatController::class, 'index']);
            });
    });
