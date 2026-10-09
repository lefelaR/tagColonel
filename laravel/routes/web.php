<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Infrastructure liveness only. Domain controllers arrive with the presentation layer.
Route::get('/health', function () {
    return response()->json([
        'service' => 'laravel',
        'status' => 'ok',
        'db_host' => config('database.connections.mysql.host'),
        'redis_host' => config('database.redis.default.host'),
    ]);
});
