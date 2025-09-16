# Implementasi Temporary Patient Token System

## Overview
Sistem ini mengimplementasikan **Cross-Device Stateless Authentication with Temporary Bearer Token** untuk akses data pasien melalui QR Code yang aman.

## Problem Solved
- **Before**: QR code URLs tetap dapat diakses bahkan setelah Device 1 offline, menciptakan risiko keamanan
- **After**: QR code menggunakan temporary token yang:
  - Memiliki waktu expiry
  - One-time use (otomatis revoked setelah digunakan)
  - Dapat di-revoke manual
  - Tracking lengkap untuk audit

## Files Created/Modified

### 1. Database Migration
**File**: `database/migrations/2025_08_31_153209_create_temporary_patient_tokens_table.php`
```bash
# Untuk menjalankan migration:
php artisan migrate
```

**Schema**:
- `patient_id` - Foreign key ke table patients
- `token` - Unique token string (64 characters)
- `expires_at` - Waktu expiry token
- `created_by_user_id` - User yang generate token
- `is_used` - Boolean untuk one-time use
- `used_at` - Waktu token digunakan
- `created_from_ip` - IP address untuk tracking
- `user_agent` - Browser/device info

### 2. Model
**File**: `app/Models/TemporaryPatientToken.php`

**Key Methods**:
- `createForPatient()` - Generate token baru
- `isValid()` - Check apakah token masih valid
- `markAsUsed()` - Tandai token sebagai sudah digunakan
- `findValidToken()` - Cari token valid
- `revokeAllForPatient()` - Revoke semua token patient
- `cleanupExpiredTokens()` - Cleanup otomatis

### 3. API Controller
**File**: `app/Http/Controllers/Api/TemporaryPatientTokenController.php`

**Endpoints**:
- `POST /api/patient/tokens/generate/{patientId}` - Generate token
- `GET /api/patient/token/{token}` - Access data via token (PUBLIC)
- `GET /api/patient/tokens/active` - List active tokens
- `DELETE /api/patient/tokens/revoke/{token}` - Revoke specific token
- `DELETE /api/patient/tokens/revoke-all` - Revoke all tokens

### 4. Routes
**File**: `routes/api.php`
- Added public route untuk token access
- Added protected routes untuk token management

### 5. Cleanup Command
**File**: `app/Console/Commands/CleanupExpiredTokens.php`
```bash
# Manual cleanup:
php artisan tokens:cleanup

# Cleanup dengan custom retention:
php artisan tokens:cleanup --days=3
```

## API Usage Examples

### 1. Generate Token (Authenticated)
```bash
POST /api/patient/tokens/generate/123
Authorization: Bearer {user_token}
Content-Type: application/json

{
  "expiration_minutes": 60
}
```

**Response**:
```json
{
  "status": "success",
  "message": "Temporary token generated successfully",
  "data": {
    "token": "abc123...xyz789",
    "expires_at": "2025-01-31T16:32:09.000000Z",
    "expiration_minutes": 60,
    "qr_url": "http://localhost:8000/api/patient/token/abc123...xyz789",
    "patient": {
      "id": 123,
      "patient_id": "P001",
      "full_name": "John Doe"
    }
  }
}
```

### 2. Access Patient Data via Token (Public)
```bash
GET /api/patient/token/abc123...xyz789
```

**Response**:
```json
{
  "status": "success", 
  "message": "Patient data retrieved successfully",
  "data": {
    "patient_data": {
      "info": {...},
      "latest_record": {...},
      "medical_history": [...],
      "genetic_results": [...],
      "allergies": [...]
    },
    "token_info": {
      "created_at": "2025-01-31T15:32:09.000000Z",
      "created_by": "Dr. Smith",
      "expires_at": "2025-01-31T16:32:09.000000Z",
      "is_used": true
    }
  }
}
```

### 3. List Active Tokens (Authenticated)
```bash
GET /api/patient/tokens/active
Authorization: Bearer {user_token}
```

### 4. Revoke Token (Authenticated)
```bash
DELETE /api/patient/tokens/revoke/abc123...xyz789
Authorization: Bearer {user_token}
```

## Security Features

### 1. Token Generation
- **Unique**: 64-character random string
- **Collision-free**: Checks uniqueness before creation
- **Tracked**: IP, User Agent, Creator logging

### 2. Token Validation
- **Expiry check**: Automatic expiration
- **One-time use**: Auto-revoked after access
- **Manual revocation**: Can be revoked anytime
- **Audit trail**: Complete access logging

### 3. Cleanup Strategy
- **Automatic**: Via scheduled command
- **Configurable**: Retention period
- **Safe**: Only deletes truly expired tokens

## QR Code Integration

### Generate QR Code
```php
// Frontend/Mobile app dapat generate QR dari URL:
$qrUrl = $response['data']['qr_url'];

// Example QR content:
// http://localhost:8000/api/patient/token/abc123...xyz789
```

### Access Flow
1. **Device 1**: Generate token → Create QR code
2. **Device 2**: Scan QR → Call public API endpoint
3. **System**: Validate token → Return data → Revoke token
4. **Security**: Token unusable for future access

## Production Deployment

### 1. Environment Variables
```bash
# .env
APP_URL=https://your-domain.com
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
# ... other configs
```

### 2. Migration
```bash
php artisan migrate --force
```

### 3. Scheduled Cleanup (Optional)
```bash
# Add to crontab:
0 2 * * * cd /path/to/app && php artisan tokens:cleanup
```

## Testing Checklist

- [ ] Migration berhasil dijalankan
- [ ] Generate token via API
- [ ] Access data via token (public endpoint)
- [ ] Token auto-revoked setelah digunakan
- [ ] Manual revocation works
- [ ] List active tokens
- [ ] Expired tokens rejected
- [ ] Cleanup command works
- [ ] Logging berfungsi
- [ ] QR code integration

## Error Handling

### Common Errors:
- **401**: Token invalid/expired
- **404**: Patient/Token not found
- **500**: Server errors

### Logging:
- Token generation
- Token access
- Token revocation
- Cleanup operations
- Error conditions

## Performance Considerations

### Indexes:
- `(token, expires_at)` - Fast token lookup
- `(patient_id, is_used)` - Efficient revocation
- Foreign keys for referential integrity

### Cleanup:
- Regular cleanup prevents table bloat
- Configurable retention period
- Batch operations for efficiency

---

## Next Steps
1. Run migration: `php artisan migrate`
2. Test API endpoints
3. Integrate QR code generation in frontend
4. Setup scheduled cleanup
5. Deploy to production
