<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/upload', [UploadController::class, 'upload']);
Route::get('/view', [UploadController::class, 'view']);
Route::get('/test-token', [UploadController::class, 'testToken']);
