#!/bin/bash

# Script untuk mengupload environment variables ke Vercel
# Berdasarkan file .env.vercel

echo "Setting up Vercel Environment Variables..."

# Basic Application Settings
vercel env add APP_NAME --value="PG Card MVP" --yes
vercel env add APP_ENV --value="production" --yes
vercel env add APP_KEY --value="base64:6w80b2/0FoV0rvsHQL/4xpR1Lyvy3LtFcK9G+CN4iwc=" --yes
vercel env add APP_DEBUG --value="false" --yes
vercel env add APP_TIMEZONE --value="Asia/Jakarta" --yes
vercel env add APP_URL --value="https://laravel-6ve8lj9y3cx-xinzzus-projects.vercel.app" --yes
vercel env add APP_LOCALE --value="id" --yes
vercel env add APP_FALLBACK_LOCALE --value="en" --yes

# Vercel Detection
vercel env add VERCEL --value="1" --yes
vercel env add VERCEL_ENV --value="production" --yes

# Logging
vercel env add LOG_CHANNEL --value="stderr" --yes
vercel env add LOG_LEVEL --value="error" --yes
vercel env add LOG_DEPRECATIONS_CHANNEL --value="null" --yes
vercel env add LOG_STACK --value="stderr" --yes

# Database (Supabase)
vercel env add DATABASE_URL --value="postgresql://postgres:12345678@db.obwzncalwdmfjnkkqpjh.supabase.co:5432/postgres" --yes
vercel env add DB_CONNECTION --value="pgsql" --yes
vercel env add DB_HOST --value="db.obwzncalwdmfjnkkqpjh.supabase.co" --yes
vercel env add DB_PORT --value="5432" --yes
vercel env add DB_DATABASE --value="postgres" --yes
vercel env add DB_USERNAME --value="postgres" --yes
vercel env add DB_PASSWORD --value="12345678" --yes

# Session & Cache
vercel env add SESSION_DRIVER --value="cookie" --yes
vercel env add SESSION_LIFETIME --value="120" --yes
vercel env add SESSION_ENCRYPT --value="true" --yes
vercel env add CACHE_STORE --value="array" --yes
vercel env add CACHE_PREFIX --value="pgcard" --yes

# Queue
vercel env add QUEUE_CONNECTION --value="sync" --yes

# Mail
vercel env add MAIL_MAILER --value="log" --yes
vercel env add MAIL_FROM_ADDRESS --value="noreply@pgcard.com" --yes
vercel env add MAIL_FROM_NAME --value="PG Card MVP" --yes

# Filesystem
vercel env add FILESYSTEM_DISK --value="local" --yes

# App Specific
vercel env add TEMP_TOKEN_EXPIRE_MINUTES --value="60" --yes
vercel env add TEMP_TOKEN_MAX_USES --value="10" --yes
vercel env add TEMP_TOKEN_LENGTH --value="32" --yes

echo "Environment variables setup completed!"