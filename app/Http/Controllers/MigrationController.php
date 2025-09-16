<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MigrationController extends Controller
{
    /**
     * Run database migrations
     */
    public function migrate(Request $request)
    {
        try {
            // Security check - only allow in production with special token
            $migrationToken = $request->header('Migration-Token');
            if ($migrationToken !== config('app.migration_token', 'secure-migration-key-123')) {
                return response()->json([
                    'error' => 'Unauthorized migration attempt'
                ], 401);
            }

            Log::info('Starting database migration via endpoint');

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            Log::info('Migration completed', [
                'output' => $migrateOutput
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Database migration completed successfully',
                'output' => $migrateOutput
            ]);

        } catch (\Exception $e) {
            Log::error('Migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Migration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run fresh migrations (drop all tables and recreate)
     */
    public function migrateFresh(Request $request)
    {
        try {
            // Security check - only allow in production with special token
            $migrationToken = $request->header('Migration-Token');
            if ($migrationToken !== config('app.migration_token', 'secure-migration-key-123')) {
                return response()->json([
                    'error' => 'Unauthorized migration attempt'
                ], 401);
            }

            Log::info('Starting fresh database migration via endpoint');

            // Run fresh migrations
            Artisan::call('migrate:fresh', ['--force' => true]);
            $migrateOutput = Artisan::output();

            // Run seeders if needed
            Artisan::call('db:seed', ['--force' => true]);
            $seedOutput = Artisan::output();

            Log::info('Fresh migration completed', [
                'migrate_output' => $migrateOutput,
                'seed_output' => $seedOutput
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fresh database migration completed successfully',
                'migrate_output' => $migrateOutput,
                'seed_output' => $seedOutput
            ]);

        } catch (\Exception $e) {
            Log::error('Fresh migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Fresh migration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check database connection and status
     */
    public function status()
    {
        try {
            // Test database connection
            $pdo = \DB::connection()->getPdo();
            
            // Get migration status
            Artisan::call('migrate:status');
            $statusOutput = Artisan::output();

            return response()->json([
                'success' => true,
                'database_connected' => true,
                'migration_status' => $statusOutput
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'database_connected' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}