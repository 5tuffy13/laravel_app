<?php

use App\Http\Controllers\Api\ArticleApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('articles', ArticleApiController::class);
