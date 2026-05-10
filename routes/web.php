<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AlbumController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES - Wedding Upload System
|--------------------------------------------------------------------------
*/

// Redirect root to upload page (optional but clean)


//Google Page
Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::get('/logout', [GoogleController::class, 'logout']);

//Album page


Route::middleware('google.auth')->group(function () {

    Route::get('/', [UploadController::class, 'index']);

    Route::post('/upload', [UploadController::class, 'uploadFile']);

    Route::get('/view', [UploadController::class, 'view']);
    Route::get('/album', [AlbumController::class, 'index']);



});
