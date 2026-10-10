<?php

use Illuminate\Support\Facades\Route;
use Presentation\Http\Controllers\AlertController;
use Presentation\Http\Controllers\CartController;
use Presentation\Http\Controllers\CourseController;

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}/carts/live', [CartController::class, 'live'])->whereNumber('id');
Route::post('/ingest', [CartController::class, 'ingest']);
Route::get('/alerts', [AlertController::class, 'index']);
Route::post('/alerts', [AlertController::class, 'store']);
