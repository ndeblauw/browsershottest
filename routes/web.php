<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrowsershotController;

Route::get('/', function () {
    return view('welcome');
});

// Browsershot test routes
Route::get('/testimage', [BrowsershotController::class, 'testImage']);
Route::get('/screenshot', [BrowsershotController::class, 'screenshot']);
