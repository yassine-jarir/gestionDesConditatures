<?php

use App\Http\Controllers\CvController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/cv/upload',[CvController::class, 'uploadCv']);
});

