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

Route::get('/dashboard', function () {
    $user = session('user');

    if (!$user) {
        return redirect('/auth/google');
    }
    else{

    return view('home', compact('user'));
    }

}); 


// UPLOAD PAGE (Blade UI)
Route::get('/', [UploadController::class, 'index']);

// HANDLE FILE UPLOAD (Google Drive)
Route::post('/upload', [UploadController::class, 'uploadFile']);


// OPTIONAL: view Google Drive files page
Route::get('/view', [UploadController::class, 'view']);

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::post('/upload-multiple', [GoogleDriveController::class, 'uploadMultiple']);


Route::get('/logout', [GoogleController::class, 'logout']);

//Album page
Route::get('/album', [AlbumController::class, 'index']);
Route::get('/album/delete/{fileId}', [AlbumController::class, 'delete']);
Route::get('/api/album/{category?}', [AlbumController::class, 'api']);


