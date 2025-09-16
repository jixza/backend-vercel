<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use App\Models\TemporaryPatientToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    use ApiResponse;

    // Register a new mobile user
    public function register(Request $request)
    {
        $request->validate([
            'medical_record_number' => 'required|unique:mobile_users,medical_record_number',
            'password' => 'required|min:6',
        ]);

        $user = MobileUser::create([
            'medical_record_number' => $request->medical_record_number,
            'password' => bcrypt($request->password),
        ]);

        return $this->successResponse('User registered successfully', $user);
    }

    // Login user
    public function login(Request $request)
    {
        $request->validate([
            'medical_record_number' => 'required',
            'password' => 'required',
        ]);

        $user = MobileUser::where('medical_record_number', $request->medical_record_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 1001);
        }

        $token = $user->createToken('mobile_token')->plainTextToken;

        return $this->successResponse('Login successful', ['access_token' => $token]);
    }

    // Logout user
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            // Revoke semua temporary patient tokens yang dibuat oleh user ini
            $revokedTokensCount = TemporaryPatientToken::where('created_by_user_id', $user->id)
                ->where('is_used', false)
                ->update([
                    'is_used' => true,
                    'used_at' => now()
                ]);
            
            // Revoke semua access tokens
            $user->tokens()->delete();
            
            Log::info('User logged out', [
                'user_id' => $user->id,
                'revoked_temporary_tokens' => $revokedTokensCount
            ]);
            
            return $this->successResponse('Logged out successfully', [
                'revoked_temporary_tokens' => $revokedTokensCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error during logout', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Logout failed', 500, 500);
        }
    }

    // Get user details
    public function user(Request $request)
    {
        return $this->successResponse('User retrieved successfully', $request->user());
    }
}
