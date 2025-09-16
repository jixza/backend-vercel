@echo off
REM ====================================================================
REM Vercel Environment Variables Setup Script
REM PG Card MVP - Supabase PostgreSQL Configuration
REM ====================================================================

echo.
echo ===================================================
echo   Setting up Vercel Environment Variables
echo   PG Card MVP - Supabase Configuration
echo ===================================================
echo.

REM Check if Vercel CLI is installed
vercel --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Vercel CLI is not installed
    echo Please install with: npm install -g vercel
    pause
    exit /b 1
)

echo ✅ Vercel CLI detected
echo.
echo 🔧 Setting up environment variables for production...
echo.

REM Core Application Settings
echo [1/15] Setting core application variables...
vercel env add APP_NAME production
echo PG Card MVP

vercel env add APP_ENV production
echo production

vercel env add APP_KEY production
echo base64:6w80b2/0FoV0rvsHQL/4xpR1Lyvy3LtFcK9G+CN4iwc=

vercel env add APP_DEBUG production
echo false

vercel env add APP_TIMEZONE production
echo Asia/Jakarta

echo ⚠️  Please update APP_URL manually in Vercel dashboard after deployment
vercel env add APP_URL production
echo https://your-app-name.vercel.app

vercel env add APP_LOCALE production
echo id

vercel env add APP_FALLBACK_LOCALE production
echo en

REM Logging Configuration
echo [2/15] Setting logging configuration...
vercel env add LOG_CHANNEL production
echo stderr

vercel env add LOG_LEVEL production
echo error

REM Database Configuration (Supabase)
echo [3/15] Setting Supabase database configuration...
vercel env add DATABASE_URL production
echo postgresql://postgres:12345678@db.obwzncalwdmfjnkkqpjh.supabase.co:5432/postgres

vercel env add DB_CONNECTION production
echo pgsql

vercel env add DB_HOST production
echo db.obwzncalwdmfjnkkqpjh.supabase.co

vercel env add DB_PORT production
echo 5432

vercel env add DB_DATABASE production
echo postgres

vercel env add DB_USERNAME production
echo postgres

vercel env add DB_PASSWORD production
echo 12345678

REM Session Configuration
echo [4/15] Setting session configuration...
vercel env add SESSION_DRIVER production
echo cookie

vercel env add SESSION_LIFETIME production
echo 120

vercel env add SESSION_ENCRYPT production
echo true

REM Cache Configuration
echo [5/15] Setting cache configuration...
vercel env add CACHE_STORE production
echo array

vercel env add CACHE_PREFIX production
echo pgcard

REM Queue Configuration
echo [6/15] Setting queue configuration...
vercel env add QUEUE_CONNECTION production
echo sync

REM Mail Configuration
echo [7/15] Setting mail configuration...
vercel env add MAIL_MAILER production
echo log

vercel env add MAIL_FROM_ADDRESS production
echo noreply@your-domain.com

vercel env add MAIL_FROM_NAME production
echo PG Card MVP

REM Filesystem Configuration
echo [8/15] Setting filesystem configuration...
vercel env add FILESYSTEM_DISK production
echo local

REM CORS Configuration
echo [9/15] Setting CORS configuration...
vercel env add CORS_ALLOWED_ORIGINS production
echo https://your-app-name.vercel.app

vercel env add CORS_ALLOWED_HEADERS production
echo *

vercel env add CORS_ALLOWED_METHODS production
echo GET,POST,PUT,DELETE,OPTIONS

vercel env add CORS_SUPPORTS_CREDENTIALS production
echo true

REM Rate Limiting
echo [10/15] Setting rate limiting...
vercel env add RATE_LIMIT_API production
echo 60,1

vercel env add RATE_LIMIT_LOGIN production
echo 5,1

REM Token Configuration
echo [11/15] Setting token configuration...
vercel env add TEMP_TOKEN_EXPIRE_MINUTES production
echo 60

vercel env add TEMP_TOKEN_MAX_USES production
echo 10

vercel env add TEMP_TOKEN_LENGTH production
echo 32

REM QR Code Configuration
echo [12/15] Setting QR code configuration...
vercel env add QR_CODE_SIZE production
echo 200

vercel env add QR_CODE_MARGIN production
echo 2

vercel env add QR_CODE_ERROR_CORRECTION production
echo M

REM Upload Configuration
echo [13/15] Setting upload configuration...
vercel env add MAX_UPLOAD_SIZE production
echo 4096

vercel env add ALLOWED_FILE_TYPES production
echo jpg,jpeg,png,pdf

REM Pagination
echo [14/15] Setting pagination...
vercel env add PAGINATION_DEFAULT_SIZE production
echo 15

vercel env add PAGINATION_MAX_SIZE production
echo 100

REM Campus Settings
echo [15/15] Setting campus configuration...
vercel env add CAMPUS_NAME production
echo Universitas Campus

vercel env add CAMPUS_CODE production
echo UNIV

vercel env add CAMPUS_TIMEZONE production
echo Asia/Jakarta

vercel env add CAMPUS_API_ENABLED production
echo false

vercel env add CAMPUS_ALLOWED_IPS production
echo *

echo.
echo ===================================================
echo           ✅ Environment Variables Set!
echo ===================================================
echo.
echo 🔍 Verify your environment variables:
echo    vercel env ls
echo.
echo 🚀 Deploy your application:
echo    vercel --prod
echo.
echo ⚠️  Important Notes:
echo    1. Update APP_URL after getting your Vercel deployment URL
echo    2. Update CORS_ALLOWED_ORIGINS with your actual domains
echo    3. Consider generating a new APP_KEY for production
echo    4. Test database connection after deployment
echo.
echo 💡 Useful Commands:
echo    - View all env vars: vercel env ls
echo    - Update env var: vercel env add VARIABLE_NAME production
echo    - Remove env var: vercel env rm VARIABLE_NAME production
echo    - Pull env vars: vercel env pull .env.local
echo.

pause