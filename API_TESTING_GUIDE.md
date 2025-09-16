# Manual API Testing Guide

## Setup untuk Testing

### 1. Pastikan Database Berjalan
```bash
# Start local MySQL (XAMPP/MAMP)
# Atau gunakan database Railway untuk testing

# Run migrations
php artisan migrate
```

### 2. Buat Test User & Patient
```bash
# Buat user baru via Tinker
php artisan tinker
```

```php
// Di Tinker console:
$user = App\Models\User::create([
    'name' => 'Test Doctor',
    'email' => 'doctor@test.com', 
    'password' => bcrypt('password123'),
    'patient_id' => 'P001'
]);

$patient = App\Models\Patient::create([
    'patient_id' => 'P001',
    'full_name' => 'John Doe',
    'date_of_birth' => '1990-01-01',
    'gender' => 'male',
    'phone_number' => '08123456789',
    'address' => 'Jakarta'
]);

echo "User ID: " . $user->id;
echo "\nPatient ID: " . $patient->id;
```

### 3. Generate User Token
```bash
# Login untuk mendapatkan token
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "doctor@test.com",
    "password": "password123"
  }'
```

Copy token dari response untuk digunakan di testing berikutnya.

---

## Test Cases

### 1. Generate Temporary Token

**Request:**
```bash
curl -X POST http://localhost:8000/api/patient/tokens/generate/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_USER_TOKEN" \
  -d '{
    "expiration_minutes": 30
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Temporary token generated successfully",
  "data": {
    "token": "abc123def456...",
    "expires_at": "2025-01-31T16:30:00.000000Z",
    "expiration_minutes": 30,
    "qr_url": "http://localhost:8000/api/patient/token/abc123def456...",
    "patient": {
      "id": 1,
      "patient_id": "P001", 
      "full_name": "John Doe"
    }
  }
}
```

### 2. Access Patient Data (Public Endpoint)

**Request:**
```bash
curl -X GET http://localhost:8000/api/patient/token/abc123def456...
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Patient data retrieved successfully", 
  "data": {
    "patient_data": {
      "info": {
        "patient_id": "P001",
        "full_name": "John Doe",
        "initials": "JD",
        "date_of_birth": "1990-01-01",
        "age": 35,
        "gender": "male"
      },
      "latest_record": null,
      "medical_history": [],
      "genetic_results": [],
      "allergies": []
    },
    "token_info": {
      "created_at": "2025-01-31T15:30:00.000000Z",
      "created_by": "Test Doctor",
      "expires_at": "2025-01-31T16:30:00.000000Z",
      "is_used": true
    }
  }
}
```

### 3. Try Accessing Used Token (Should Fail)

**Request:**
```bash
curl -X GET http://localhost:8000/api/patient/token/abc123def456...
```

**Expected Response:**
```json
{
  "status": "error",
  "message": "Invalid or expired token",
  "error_code": 401
}
```

### 4. List Active Tokens

**Request:**
```bash
curl -X GET http://localhost:8000/api/patient/tokens/active \
  -H "Authorization: Bearer YOUR_USER_TOKEN"
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Active tokens retrieved successfully",
  "data": []
}
```

### 5. Generate Multiple Tokens

**Generate Token 1:**
```bash
curl -X POST http://localhost:8000/api/patient/tokens/generate/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_USER_TOKEN" \
  -d '{"expiration_minutes": 60}'
```

**Generate Token 2:**
```bash
curl -X POST http://localhost:8000/api/patient/tokens/generate/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_USER_TOKEN" \
  -d '{"expiration_minutes": 30}'
```

**Check Active Tokens:**
```bash
curl -X GET http://localhost:8000/api/patient/tokens/active \
  -H "Authorization: Bearer YOUR_USER_TOKEN"
```

Expected: Only the latest token should be active (previous ones revoked).

### 6. Manual Token Revocation

**Get Token from Active List and Revoke:**
```bash
curl -X DELETE http://localhost:8000/api/patient/tokens/revoke/TOKEN_HERE \
  -H "Authorization: Bearer YOUR_USER_TOKEN"
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Token revoked successfully"
}
```

### 7. Revoke All Tokens

**Generate Multiple Tokens First:**
```bash
# Generate 3 tokens
curl -X POST http://localhost:8000/api/patient/tokens/generate/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_USER_TOKEN" \
  -d '{"expiration_minutes": 60}'

# Check you have 1 active token
curl -X GET http://localhost:8000/api/patient/tokens/active \
  -H "Authorization: Bearer YOUR_USER_TOKEN"

# Revoke all
curl -X DELETE http://localhost:8000/api/patient/tokens/revoke-all \
  -H "Authorization: Bearer YOUR_USER_TOKEN"
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "All tokens revoked successfully",
  "data": {
    "revoked_count": 1
  }
}
```

### 8. Test Expired Token

**Generate Token with Short Expiry:**
```bash
curl -X POST http://localhost:8000/api/patient/tokens/generate/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_USER_TOKEN" \
  -d '{"expiration_minutes": 1}'
```

**Wait 2 minutes, then try to access:**
```bash
curl -X GET http://localhost:8000/api/patient/token/TOKEN_HERE
```

**Expected Response:**
```json
{
  "status": "error",
  "message": "Invalid or expired token",
  "error_code": 401
}
```

### 9. Test Invalid Token

**Request:**
```bash
curl -X GET http://localhost:8000/api/patient/token/invalid_token_123
```

**Expected Response:**
```json
{
  "status": "error",
  "message": "Invalid or expired token", 
  "error_code": 401
}
```

---

## Testing Cleanup Command

### Manual Test
```bash
# Generate some test tokens
php artisan tinker
```

```php
// Create old expired tokens
$patient = App\Models\Patient::first();
$user = App\Models\User::first();

// Create token expired 10 days ago
$oldToken = App\Models\TemporaryPatientToken::create([
    'patient_id' => $patient->id,
    'token' => Str::random(64),
    'expires_at' => now()->subDays(10),
    'created_by_user_id' => $user->id,
    'created_at' => now()->subDays(10)
]);

// Create recent expired token  
$recentToken = App\Models\TemporaryPatientToken::create([
    'patient_id' => $patient->id,
    'token' => Str::random(64),
    'expires_at' => now()->subHours(1),
    'created_by_user_id' => $user->id
]);

echo "Old token ID: " . $oldToken->id;
echo "\nRecent token ID: " . $recentToken->id;
```

**Run Cleanup:**
```bash
php artisan tokens:cleanup --days=7
```

**Verify:**
```bash
php artisan tinker
```
```php
// Check if old token is deleted
App\Models\TemporaryPatientToken::find(OLD_TOKEN_ID); // Should return null

// Check if recent token still exists
App\Models\TemporaryPatientToken::find(RECENT_TOKEN_ID); // Should return record
```

---

## Expected Behaviors

### Security Features Working:
- ✅ Token only works once (auto-revoked after use)
- ✅ Expired tokens rejected
- ✅ Invalid tokens rejected
- ✅ Generating new token revokes old ones
- ✅ Manual revocation works
- ✅ Public endpoint doesn't require auth
- ✅ Management endpoints require auth

### Logging Working:
- ✅ Token generation logged
- ✅ Token access logged  
- ✅ Token revocation logged
- ✅ Error attempts logged

### Performance:
- ✅ Fast token lookup via index
- ✅ Efficient revocation queries
- ✅ Cleanup command works

---

## Troubleshooting

### Common Issues:

**1. Database Connection Error**
```bash
# Check .env database config
# Start MySQL service
# Run: php artisan migrate
```

**2. Token Generation Fails**
```bash
# Check user authentication
# Verify patient exists
# Check logs: storage/logs/laravel.log
```

**3. Token Access Fails**
```bash
# Verify token is valid and not used
# Check token format (64 characters)
# Verify API URL is correct
```

**4. Route Not Found**
```bash
# Run: php artisan route:list
# Check if routes are properly registered
```

---

## Production Deployment Testing

### 1. Environment Check
```bash
# Verify production database
php artisan migrate --force

# Test basic functionality
php artisan tinker
```

### 2. Load Testing
```bash
# Generate many tokens
for i in {1..100}; do
  curl -X POST https://your-domain.com/api/patient/tokens/generate/1 \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"expiration_minutes": 60}'
done
```

### 3. Security Testing
```bash
# Test with invalid tokens
# Test with expired tokens  
# Test without authentication
# Test SQL injection attempts
```

### 4. Monitoring
```bash
# Setup log monitoring
tail -f storage/logs/laravel.log | grep -i "token"

# Monitor database performance
# Check token table size
# Verify indexes are used
```
