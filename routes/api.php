<?php

 use App\Http\Controllers\CandidatController;
 use App\Http\Controllers\RecruteurController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CandidatureController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:jwt');
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:jwt');

 
Route::middleware('auth:jwt')->group(function () {
    // condidat 
    Route::put('/candidat/profil', [CandidatController::class, 'updateProfile']);
    Route::get('/getalljob', [AdminController::class, 'getAllJobs']);
    Route::get('/cv/getcv', [CandidatController::class, 'getCvs']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/cv/upload', [CandidatController::class, 'uploadCv']);
    Route::post('/job/apply', [CandidatController::class, 'apply']);


    Route::get('/myJobs', [RecruteurController::class, 'getMyJobs']);
    Route::delete('/job/{job}', [RecruteurController::class, 'deleteJob']);
    Route::post('/jobs', [AdminController::class, 'createJob']);

    Route::get('/getalljob', [AdminController::class, 'getAllJobs']);
    Route::post('admin/jobs', [AdminController::class, 'createJob']);
    Route::delete('admin/jobs/{jobId}', [AdminController::class, 'deleteJob']);
    Route::get('admin/users', [AdminController::class, 'getAllUsers']);
    Route::put('admin/users/{userId}', [AdminController::class, 'updateUser']);
    Route::delete('admin/users/{userId}', [AdminController::class, 'deleteUser']);





    Route::get('/export/applications', [CandidatController::class, 'export']);    

 });
 
 Route::get('/testEmail', function () {
    Mail::raw('This is a test email', function ($message) {
        $message->to('yassinejr7@gmail.com')
                ->subject('Test Email');
    });

    return "Test email sent! Check Mailtrap.";  
});

//  Route::post('/postuler-multiple', [ApplicationController::class, 'postulerPlusieurs'])->middleware('can:apply-job');
