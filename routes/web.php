<?php

use App\Http\Controllers\Api\TemporaryPatientTokenController;
use App\Http\Controllers\Web\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Secure patient data access via temporary token (Web Interface)
Route::get('/patient/token/{token}', [TemporaryPatientTokenController::class, 'showPatientByToken'])->name('patient.token');

// Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patient.show');
// DISABLED: Route ini tidak aman - bisa diakses dengan patient ID langsung
// Gunakan /patient/token/{token} melalui API untuk akses yang aman
