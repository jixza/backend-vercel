<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TemporaryPatientToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'token',
        'expires_at',
        'created_by_user_id',
        'is_used',
        'used_at',
        'created_from_ip',
        'user_agent'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Relasi ke Patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relasi ke User yang membuat token
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Generate token unik
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Buat token baru untuk patient
     */
    public static function createForPatient(
        int $patientId,
        int $userId,
        int $expirationMinutes = 60,
        ?string $ip = null,
        ?string $userAgent = null
    ): self {
        return self::create([
            'patient_id' => $patientId,
            'token' => self::generateUniqueToken(),
            'expires_at' => Carbon::now()->addMinutes($expirationMinutes),
            'created_by_user_id' => $userId,
            'created_from_ip' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Validasi apakah token masih valid
     */
    public function isValid(): bool
    {
        return !$this->is_used && 
               $this->expires_at->isFuture();
    }

    /**
     * Tandai token sebagai sudah digunakan
     */
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => Carbon::now(),
        ]);
    }

    /**
     * Scope untuk token yang masih valid
     */
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
                    ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope untuk token yang sudah expired
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', Carbon::now());
    }

    /**
     * Cari token valid berdasarkan token string
     */
    public static function findValidToken(string $token): ?self
    {
        return self::where('token', $token)
                   ->valid()
                   ->first();
    }

    /**
     * Revoke semua token aktif untuk patient
     */
    public static function revokeAllForPatient(int $patientId): int
    {
        return self::where('patient_id', $patientId)
                   ->where('is_used', false)
                   ->update(['is_used' => true, 'used_at' => Carbon::now()]);
    }

    /**
     * Cleanup token yang sudah expired
     */
    public static function cleanupExpiredTokens(): int
    {
        return self::expired()->delete();
    }
}
