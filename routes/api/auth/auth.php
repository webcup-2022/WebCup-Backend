<?php

use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\SocialLoginController;
use App\Http\Controllers\Api\Auth\VerificationController;

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    /**
     * auth action
     */
    Route::group(['middleware' => 'throttle:20,5'], function(){
        Route::post('logout', [MeController::class, 'logout']);
        Route::post('me', [MeController::class, 'index']);
    });

    /**
     * form authentication
     */
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    /**
     * form verification
    */

    Route::get('email/verify/{id}/{hash}', [VerificationController::class,'verify'])->name('verification.verify')->middleware(['signed']);
    /**
     * social authentication
     */
    Route::get('/login/{service}', [SocialLoginController::class, 'redirect']);
    Route::get('/login/{service}/callback', [SocialLoginController::class, 'callback']);
});
