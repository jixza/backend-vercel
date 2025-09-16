<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\TemporaryPatientToken;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TemporaryPatientTokenController extends Controller
{
    use \App\ApiResponse;

    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Generate temporary token untuk patient
     */
    public function generateToken(Request $request, $patientId)
    {
        try {
            $user = Auth::user();
            
            // Validasi apakah patient ada
            $patient = Patient::find($patientId);
            if (!$patient) {
                return $this->errorResponse('Patient not found', 404, 404);
            }

            // Validasi request
            $validated = $request->validate([
                'expiration_minutes' => 'integer|min:5|max:1440', // 5 menit - 24 jam
            ]);

            $expirationMinutes = $validated['expiration_minutes'] ?? 60; // default 1 jam

            // Revoke token lama yang masih aktif untuk patient ini
            TemporaryPatientToken::revokeAllForPatient($patientId);

            // Buat token baru
            $token = TemporaryPatientToken::createForPatient(
                $patientId,
                $user->id,
                $expirationMinutes,
                $request->ip(),
                $request->userAgent()
            );

            // Generate URL untuk QR Code
            $qrUrl = config('app.url') . "/api/patient/token/{$token->token}";

            Log::info('Temporary token generated', [
                'patient_id' => $patientId,
                'token_id' => $token->id,
                'created_by' => $user->id,
                'expires_at' => $token->expires_at,
                'ip' => $request->ip()
            ]);

            return $this->successResponse('Temporary token generated successfully', [
                'token' => $token->token,
                'expires_at' => $token->expires_at,
                'expiration_minutes' => $expirationMinutes,
                'qr_url' => $qrUrl,
                'patient' => [
                    'id' => $patient->id,
                    'patient_id' => $patient->patient_id,
                    'full_name' => $patient->full_name
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating temporary token', [
                'patient_id' => $patientId,
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Failed to generate token', 500, 500);
        }
    }

    /**
     * Access patient data menggunakan temporary token
     */
    public function accessPatientByToken($token)
    {
        try {
            // Cari token yang valid
            $tokenRecord = TemporaryPatientToken::findValidToken($token);
            
            if (!$tokenRecord) {
                Log::warning('Invalid or expired token accessed', [
                    'token' => substr($token, 0, 10) . '...',
                    'ip' => request()->ip()
                ]);
                
                return $this->errorResponse('Invalid or expired token', 401, 401);
            }

            // Ambil data patient dengan validation
            $patient = $tokenRecord->patient;
            
            if (!$patient) {
                Log::warning('Token found but patient not found', [
                    'token_id' => $tokenRecord->id,
                    'patient_id' => $tokenRecord->patient_id,
                    'token' => substr($token, 0, 10) . '...'
                ]);
                
                return $this->errorResponse('Patient data not found', 404, 404);
            }
            
            // Log patient details before calling service
            Log::info('Patient found via token', [
                'patient_id' => $patient->id,
                'patient_name' => $patient->full_name,
                'token_id' => $tokenRecord->id,
                'patient_model' => get_class($patient),
                'patient_exists' => $patient->exists,
                'patient_attributes' => array_keys($patient->getAttributes())
            ]);
            
            // TEMPORARY: Skip PatientService for now, return basic data
            if (request()->has('skip_service')) {
                return $this->successResponse('Basic patient data (service bypassed)', [
                    'patient_data' => [
                        'info' => [
                            'id' => $patient->id,
                            'full_name' => $patient->full_name,
                            'nik' => $patient->nik,
                            'gender' => $patient->gender,
                            'date_of_birth' => $patient->date_of_birth,
                            'phone' => $patient->phone,
                            'address' => $patient->address,
                        ],
                        'debug_info' => [
                            'service_bypassed' => true,
                            'patient_loaded' => true,
                            'timestamp' => now()->toISOString()
                        ]
                    ],
                    'token_info' => [
                        'created_at' => $tokenRecord->created_at,
                        'expires_at' => $tokenRecord->expires_at,
                        'can_reuse' => true
                    ]
                ]);
            }
            
            try {
                $patientData = $this->patientService->getPatientData($patient);
            } catch (\Exception $e) {
                Log::error('Error in PatientService::getPatientData', [
                    'patient_id' => $patient->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Return simplified response for debugging
                return $this->errorResponse('PatientService Error: ' . $e->getMessage(), 500, 500, [
                    'debug_fallback' => [
                        'patient_id' => $patient->id ?? 'null',
                        'patient_name' => $patient->full_name ?? 'null',
                        'error_class' => get_class($e),
                        'token_valid' => true,
                        'timestamp' => now()->toISOString()
                    ]
                ]);
            }

            // Log akses (without increment since access_count column doesn't exist)
            Log::info('Patient data accessed via temporary token', [
                'token_id' => $tokenRecord->id,
                'patient_id' => $patient->id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // DON'T mark token as used - allow multiple access until expiration
            // $tokenRecord->markAsUsed(); // Commented out for multi-use

            return $this->successResponse('Patient data retrieved successfully', [
                'patient_data' => $patientData,
                'token_info' => [
                    'created_at' => $tokenRecord->created_at,
                    'created_by' => $tokenRecord->createdBy->name,
                    'expires_at' => $tokenRecord->expires_at,
                    'is_used' => false, // Keep as reusable
                    'can_reuse' => true
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error accessing patient data by token', [
                'token' => substr($token, 0, 10) . '...',
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Failed to access patient data', 500, 500);
        }
    }

    /**
     * Revoke token tertentu
     */
    public function revokeToken($token)
    {
        try {
            $user = Auth::user();
            
            $tokenRecord = TemporaryPatientToken::where('token', $token)
                ->where('created_by_user_id', $user->id)
                ->first();
            
            if (!$tokenRecord) {
                return $this->errorResponse('Token not found or you do not have permission to revoke it', 404, 404);
            }

            $tokenRecord->markAsUsed();

            Log::info('Token manually revoked', [
                'token_id' => $tokenRecord->id,
                'patient_id' => $tokenRecord->patient_id,
                'revoked_by' => $user->id
            ]);

            return $this->successResponse('Token revoked successfully');

        } catch (\Exception $e) {
            Log::error('Error revoking token', [
                'token' => substr($token, 0, 10) . '...',
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Failed to revoke token', 500, 500);
        }
    }

    /**
     * Lihat active tokens untuk user
     */
    public function getActiveTokens()
    {
        try {
            $user = Auth::user();
            
            $tokens = TemporaryPatientToken::with(['patient'])
                ->where('created_by_user_id', $user->id)
                ->valid()
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($token) {
                    return [
                        'id' => $token->id,
                        'token' => substr($token->token, 0, 10) . '...', // Hide full token
                        'patient' => [
                            'id' => $token->patient->id,
                            'patient_id' => $token->patient->patient_id,
                            'full_name' => $token->patient->full_name
                        ],
                        'created_at' => $token->created_at,
                        'expires_at' => $token->expires_at,
                        'created_from_ip' => $token->created_from_ip
                    ];
                });

            return $this->successResponse('Active tokens retrieved successfully', $tokens);

        } catch (\Exception $e) {
            Log::error('Error getting active tokens', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Failed to get active tokens', 500, 500);
        }
    }

    /**
     * Revoke semua active tokens untuk user
     */
    public function revokeAllTokens()
    {
        try {
            $user = Auth::user();
            
            $revokedCount = TemporaryPatientToken::where('created_by_user_id', $user->id)
                ->where('is_used', false)
                ->update(['is_used' => true, 'used_at' => now()]);

            Log::info('All tokens revoked by user', [
                'user_id' => $user->id,
                'revoked_count' => $revokedCount
            ]);

            return $this->successResponse('All tokens revoked successfully', [
                'revoked_count' => $revokedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error revoking all tokens', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Failed to revoke all tokens', 500, 500);
        }
    }

    /**
     * Show patient data via token in web interface (Blade view)
     */
    public function showPatientByToken($token)
    {
        try {
            Log::info('Web interface token access attempt', [
                'token' => substr($token, 0, 10) . '...',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // Cari token yang valid - gunakan method yang sama dengan API
            $tokenRecord = TemporaryPatientToken::where('token', $token)
                ->where('expires_at', '>', now())
                ->first();
            
            Log::info('Web token lookup result', [
                'token_found' => $tokenRecord ? 'yes' : 'no',
                'token_id' => $tokenRecord->id ?? null,
                'patient_id' => $tokenRecord->patient_id ?? null,
                'expires_at' => $tokenRecord->expires_at ?? null
            ]);
            
            if (!$tokenRecord) {
                Log::warning('Web token not found or expired', [
                    'token' => substr($token, 0, 10) . '...'
                ]);
                return view('token-error', [
                    'message' => 'Token tidak valid atau sudah kedaluwarsa'
                ]);
            }

            // Ambil data patient
            $patient = $tokenRecord->patient;
            
            Log::info('Web patient lookup result', [
                'patient_found' => $patient ? 'yes' : 'no',
                'patient_id' => $tokenRecord->patient_id,
                'patient_name' => $patient->full_name ?? 'N/A'
            ]);
            
            if (!$patient) {
                Log::warning('Web patient not found', [
                    'token_id' => $tokenRecord->id,
                    'patient_id' => $tokenRecord->patient_id
                ]);
                return view('token-error', [
                    'message' => 'Data pasien tidak ditemukan'
                ]);
            }
            
            // Get patient data
            $patientData = $this->patientService->getPatientData($patient);
            
            // Log akses
            Log::info('Patient web interface accessed via token', [
                'token_id' => $tokenRecord->id,
                'patient_id' => $patient->id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return view('patient-token', [
                'patient' => $patientData,
                'token_info' => [
                    'created_at' => $tokenRecord->created_at,
                    'expires_at' => $tokenRecord->expires_at,
                    'created_by' => $tokenRecord->createdBy->name ?? 'System'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error displaying patient data via token', [
                'token' => substr($token, 0, 10) . '...',
                'error' => $e->getMessage()
            ]);

            return view('token-error', [
                'message' => 'Terjadi kesalahan saat memuat data pasien'
            ]);
        }
    }
}
