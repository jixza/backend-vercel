<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\TemporaryPatientTokenController;
use App\Http\Controllers\MigrationController;
use Illuminate\Support\Facades\Route;

// Health check endpoint untuk monitoring
Route::get('/health', function () {
    try {
        // Check database connection
        \DB::connection()->getPdo();
        
        // Check Redis connection
        \Cache::put('health_check', 'ok', 10);
        $redis_status = \Cache::get('health_check') === 'ok';
        
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'database' => 'connected',
            'redis' => $redis_status ? 'connected' : 'disconnected',
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
});

// Migration endpoints for database management
Route::prefix('migrate')->group(function () {
    Route::get('/status', [MigrationController::class, 'status']);
    Route::post('/run', [MigrationController::class, 'migrate']);
    Route::post('/fresh', [MigrationController::class, 'migrateFresh']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public route for accessing patient data with temporary token
Route::get('/patient/token/{token}', [TemporaryPatientTokenController::class, 'accessPatientByToken']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::get('/patient', [PatientController::class, 'show']);
    Route::put('/patient', [PatientController::class, 'update']);

    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);

    Route::get('/medical-record', [MedicalRecordController::class, 'show']);

    // Temporary Patient Token Routes
    Route::prefix('patient/tokens')->group(function () {
        Route::post('/generate/{patientId}', [TemporaryPatientTokenController::class, 'generateToken']);
        Route::get('/active', [TemporaryPatientTokenController::class, 'getActiveTokens']);
        Route::delete('/revoke/{token}', [TemporaryPatientTokenController::class, 'revokeToken']);
        Route::delete('/revoke-all', [TemporaryPatientTokenController::class, 'revokeAllTokens']);
    });
});
