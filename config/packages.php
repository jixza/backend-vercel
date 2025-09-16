<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Package Discovery Cache
    |--------------------------------------------------------------------------
    |
    | Path untuk package manifest cache. Di Vercel environment, kita perlu
    | menggunakan /tmp directory yang writable.
    |
    */
    'manifest' => env('PACKAGES_MANIFEST_PATH', storage_path('bootstrap/cache/packages.php')),
];