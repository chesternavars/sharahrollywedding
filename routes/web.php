<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// HOME (UPLOAD PAGE)
Route::get('/', [UploadController::class, 'index']);

// UPLOAD FILE
Route::post('/upload', [UploadController::class, 'upload']);

// VIEW ALBUM (GOOGLE DRIVE FOLDER)
Route::get('/view', [UploadController::class, 'view']);
