<?php

/**
 * Vercel Bootstrap Script
 * Setup direktori dan symlink yang diperlukan untuk Vercel
 */

// Buat direktori yang diperlukan jika belum ada
$directories = [
    '/tmp/storage',
    '/tmp/storage/logs',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set environment variables untuk path
putenv('STORAGE_PATH=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('CACHE_DRIVER=array');
putenv('SESSION_DRIVER=cookie');