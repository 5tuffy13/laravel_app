<?php

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/form', [FeedbackController::class, 'create'])->name('form.show');
Route::post('/form', [FeedbackController::class, 'store'])->name('form.store');
Route::get('/records', [FeedbackController::class, 'index'])->name('records.index');
