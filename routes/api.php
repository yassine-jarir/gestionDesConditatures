<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationConfirmation;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);

    Route::post('/cv/upload',[CvController::class, 'uploadCv']);
    Route::get('/cv/getcv',[CvController::class, 'getCvs']);

    Route::resource('/job',JobController::class);

    Route::post('/job/apply',[ApplicationController::class, 'apply']);
});


Route::get('/testEmail', function () {
    Mail::raw('This is a test email', function ($message) {
        $message->to('yassinejr7@gmail.com')
                ->subject('Test Email');
    });

    return "Test email sent! Check Mailtrap.";
});

Route::post('/postuler-multiple', [ApplicationController::class, 'postulerPlusieurs']);
