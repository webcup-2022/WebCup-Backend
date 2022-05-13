<?php

use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\SocialLoginController;

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    /**
     * auth action
     */
    Route::post('logout', [MeController::class, 'logout']);
    Route::post('set-password', [MeController::class, 'setPassword']);
    Route::group(['middleware' => 'throttle:20,5'], function(){
        Route::post('me', [MeController::class, 'index']);
    });

    /**
     * form authentication
     */
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    /**
     * social authentication
     */
    Route::get('/login/{service}', [SocialLoginController::class, 'redirect']);
    Route::get('/login/{service}/callback', [SocialLoginController::class, 'callback']);
});
