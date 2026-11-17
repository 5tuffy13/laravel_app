<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/form', [FeedbackController::class, 'create'])->name('form.show');
Route::post('/form', [FeedbackController::class, 'store'])->name('form.store');
Route::get('/records', [FeedbackController::class, 'index'])->name('records.index');

// CRUD для статей (БД Eloquent)
Route::resource('articles', ArticleController::class);
Route::post('articles/{id}/restore', [ArticleController::class, 'restore'])->name('articles.restore');
Route::post('articles/{id}/comments', [ArticleController::class, 'addComment'])->name('articles.comments.store');
