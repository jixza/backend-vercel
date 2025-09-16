<?php

namespace App\Console\Commands;

use App\Models\TemporaryPatientToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:cleanup
                            {--days=7 : Number of days to keep expired tokens before deletion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired temporary patient tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        
        $this->info("Starting cleanup of expired tokens older than {$days} days...");

        try {
            // Delete expired tokens older than specified days
            $deletedCount = TemporaryPatientToken::where('expires_at', '<', now()->subDays($days))
                ->delete();

            Log::info('Expired tokens cleanup completed', [
                'deleted_count' => $deletedCount,
                'days_threshold' => $days
            ]);

            $this->info("Cleanup completed. Deleted {$deletedCount} expired tokens.");

        } catch (\Exception $e) {
            Log::error('Error during token cleanup', [
                'error' => $e->getMessage()
            ]);

            $this->error("Error during cleanup: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
