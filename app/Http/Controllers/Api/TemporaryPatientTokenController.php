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
            Log::info('Token access attempt', [
                'token' => substr($token, 0, 10) . '...',
                'token_length' => strlen($token),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // Validate token format
            if (strlen($token) < 32) {
                Log::warning('Invalid token format', ['token_length' => strlen($token)]);
                return $this->errorResponse('Invalid token format', 400, 400);
            }

            // Cari token di database (tanpa filter valid dulu)
            $tokenRecord = TemporaryPatientToken::where('token', $token)->first();
            
            Log::info('Token search result', [
                'token_found' => $tokenRecord ? true : false,
                'token_id' => $tokenRecord ? $tokenRecord->id : null,
                'patient_id' => $tokenRecord ? $tokenRecord->patient_id : null,
                'expires_at' => $tokenRecord ? $tokenRecord->expires_at : null,
                'is_used' => $tokenRecord ? $tokenRecord->is_used : null,
                'current_time' => now()
            ]);
            
            if (!$tokenRecord) {
                Log::warning('Token not found in database', [
                    'token' => substr($token, 0, 10) . '...'
                ]);
                
                return response()->view('token-error', [
                    'error' => 'Token tidak ditemukan',
                    'message' => 'Token mungkin sudah expired atau tidak valid. Silakan minta link baru dari aplikasi.',
                    'code' => 'TOKEN_NOT_FOUND'
                ], 404);
            }

            // Cek apakah token sudah digunakan
            if ($tokenRecord->is_used) {
                Log::warning('Token already used', [
                    'token_id' => $tokenRecord->id,
                    'used_at' => $tokenRecord->used_at
                ]);
                
                return response()->view('token-error', [
                    'error' => 'Token sudah digunakan',
                    'message' => 'Link ini sudah pernah diakses sebelumnya. Untuk keamanan, setiap link hanya bisa digunakan sekali. Silakan minta link baru dari aplikasi.',
                    'code' => 'TOKEN_ALREADY_USED'
                ], 410);
            }

            // Cek apakah token sudah expired
            if ($tokenRecord->expires_at->isPast()) {
                Log::warning('Token expired', [
                    'token_id' => $tokenRecord->id,
                    'expires_at' => $tokenRecord->expires_at,
                    'current_time' => now()
                ]);
                
                return response()->view('token-error', [
                    'error' => 'Token sudah expired',
                    'message' => 'Link sudah melewati batas waktu berlaku. Silakan minta link baru dari aplikasi.',
                    'code' => 'TOKEN_EXPIRED'
                ], 410);
            }
            
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
                
                return response()->view('token-error', [
                    'error' => 'Data pasien tidak ditemukan',
                    'message' => 'Data pasien tidak tersedia di sistem. Silakan hubungi petugas medis untuk mendapatkan bantuan.',
                    'code' => 'PATIENT_NOT_FOUND'
                ], 404);
            }
            
            // Log patient details
            Log::info('Patient found via token', [
                'patient_id' => $patient->id,
                'token_id' => $tokenRecord->id
            ]);
            
            try {
                // Use simple data structure that works (same as MedicalRecordController)
                $patientData = [
                    'patient_name' => $patient->full_name ?? 'Unknown',
                    'patient_data' => ($patient->birth_place ?? 'Unknown') . ', ' . 
                                    ($patient->date_of_birth ? $patient->date_of_birth->format('d M Y') : 'Unknown'),
                    'drug_allergies' => $patient->drugAllergies->pluck('drug_name')->toArray(),
                    'prescription' => $patient->medicalRecords()->latest()->first()->prescription ?? 'Tidak ada data resep',
                    'height' => $patient->height ?? 0,
                    'weight' => $patient->weight ?? 0,
                    'bmi' => $patient->bmi ?? '0',
                    'irs1_rs1801278' => $patient->irs1_rs1801278 ?? 'Unknown',
                    'drugs_consumed' => $patient->medicalRecords()->latest()->first()->drugList ?? [],
                    'diabetes_diagnosed_since' => $patient->diabetes_diagnosis_date ? 
                        $patient->diabetes_diagnosis_date->format('d M Y') : '-'
                ];
            } catch (\Exception $e) {
                Log::error('Error getting patient data', [
                    'patient_id' => $patient->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Fallback: create basic patient data manually
                $patientData = [
                    'patient_name' => $patient->full_name ?? 'Unknown',
                    'patient_data' => ($patient->birth_place ?? 'Unknown') . ', ' . 
                                    ($patient->date_of_birth ? $patient->date_of_birth->format('d M Y') : 'Unknown'),
                    'drug_allergies' => [],
                    'prescription' => 'Tidak ada data resep',
                    'height' => $patient->height ?? 0,
                    'weight' => $patient->weight ?? 0,
                    'bmi' => $patient->bmi ?? '0',
                    'irs1_rs1801278' => $patient->irs1_rs1801278 ?? 'Unknown',
                    'drugs_consumed' => [],
                    'diabetes_diagnosed_since' => $patient->diabetes_diagnosis_date ? 
                        $patient->diabetes_diagnosis_date->format('d M Y') : '-'
                ];
            }

            // Log access and mark token as used
            Log::info('Patient data accessed via temporary token', [
                'token_id' => $tokenRecord->id,
                'patient_id' => $patient->id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // Mark token as used after first access for security
            $tokenRecord->markAsUsed();

            // Return HTML view for browser access
            return view('patient-token', [
                'patientData' => $patientData,
                'tokenInfo' => [
                    'created_at' => $tokenRecord->created_at,
                    'created_by' => $tokenRecord->createdBy->name ?? 'System',
                    'expires_at' => $tokenRecord->expires_at,
                    'is_used' => true,
                    'can_reuse' => false,
                    'message' => 'Token telah digunakan dan tidak bisa diakses lagi'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error accessing patient data by token', [
                'token' => substr($token, 0, 10) . '...',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->view('token-error', [
                'error' => 'Terjadi kesalahan server',
                'message' => 'Maaf, terjadi kesalahan saat memuat data pasien. Silakan coba lagi atau hubungi petugas medis.',
                'code' => 'SERVER_ERROR'
            ], 500);
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
                return response()->view('token-error', [
                    'error' => 'Token tidak valid',
                    'message' => 'Token tidak valid atau sudah kedaluwarsa. Silakan minta link baru.',
                    'code' => 'TOKEN_INVALID'
                ], 410);
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
                return response()->view('token-error', [
                    'error' => 'Data pasien tidak ditemukan',
                    'message' => 'Data pasien tidak tersedia di sistem. Silakan hubungi petugas medis.',
                    'code' => 'PATIENT_NOT_FOUND'
                ], 404);
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

            return view('patient.show', [
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

            return response()->view('token-error', [
                'error' => 'Terjadi kesalahan server',
                'message' => 'Maaf, terjadi kesalahan saat memuat data pasien. Silakan coba lagi atau hubungi petugas medis.',
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }
}
