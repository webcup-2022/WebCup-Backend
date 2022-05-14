<?php

use App\Http\Controllers\Api\Article\ArticleController;


Route::group([

    'middleware' => 'api',
    'prefix' => 'article'

], function ($router) {
    Route::post('update/{id}', [ArticleController::class, 'updateArticle']);
    Route::resource('posts', ArticleController::class);
});
