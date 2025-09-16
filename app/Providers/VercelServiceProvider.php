<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class VercelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Set storage path untuk Vercel
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
            $this->app->useStoragePath('/tmp/storage');
            
            // Override storage path bindings
            $this->app->singleton('path.storage', function () {
                return '/tmp/storage';
            });
            
            $this->app->singleton('path.storage.logs', function () {
                return '/tmp/storage/logs';
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Setup directories
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
            $directories = [
                '/tmp/storage',
                '/tmp/storage/logs',
                '/tmp/storage/framework',
                '/tmp/storage/framework/cache',
                '/tmp/storage/framework/cache/data',
                '/tmp/storage/framework/sessions',
                '/tmp/storage/framework/views',
                '/tmp/storage/app',
            ];
            
            foreach ($directories as $dir) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
            }
        }
    }
}