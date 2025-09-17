<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

//        's3' => [
//            'driver' => 's3',
//            'key' => env('AWS_ACCESS_KEY_ID'),
//            'secret' => env('AWS_SECRET_ACCESS_KEY'),
//            'region' => env('AWS_DEFAULT_REGION'),
//            'bucket' => env('AWS_BUCKET'),
//            'url' => env('AWS_URL'),
//            'endpoint' => env('AWS_ENDPOINT'),
//            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
//            'throw' => false,
//        ],

        'r2' => [
            'driver' => 's3',
            'key' => 'c6d8ca5fa098d5b924683ada175c8f41',
            'secret' => '1e6c55156699fd4edcfc72d4f310b90a3165e24a4e1965280888d3395634ddb3',
            'region' => 'auto',
            'bucket' => 'pod-shell',
            'url' => 'https://pod-shell.c0830890e67f445f1e9b0d4d132fc0b7.r2.cloudflarestorage.com',
            'endpoint' => 'https://c0830890e67f445f1e9b0d4d132fc0b7.r2.cloudflarestorage.com',
            'use_path_style_endpoint' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
