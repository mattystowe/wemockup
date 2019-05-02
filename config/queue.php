<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "null", "sync", "database", "beanstalkd", "sqs", "redis"
    |
    */

    'default' => env('QUEUE_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        ////////////////////////////////////
        ///
        ///
        ///
        /// Application specific
        ///
        /// To listen use php artisan queue:listen db_emails
        ///
        ///
        ///
        'db_emails' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => env('QUEUE_EMAILS'),
            'expire' => 60, // 30 seconds job length before being returned to queue
        ],

        'db_itemprocessing' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => env('QUEUE_ITEMPROCESSING'),
            'expire' => 60, // 60 seconds job length before being returned to queue
        ],
        'db_itemjobs' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => env('QUEUE_ITEMJOBS'),
            'expire' => 3600, // 1 hour for each frame
        ],
        'db_postprocesses' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => env('QUEUE_POSTPROCESSES'),
            'expire' => 3600, // 1 hour for each frame
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'expire' => 90,
        ],

        ////////////////////////////////////
        ///
        ///
        ///
        /// sqs
        ///
        ///
        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'prefix' => env('SQS_QUEUE_PREFIX'),
            'queue' => env('QUEUE_EMAILS'),
            'region' => 'eu-west-1',
        ],

        'sqs_emails' => [
            'driver' => 'sqs',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'prefix' => env('SQS_QUEUE_PREFIX'),
            'queue' => env('QUEUE_EMAILS'),
            'region' => 'eu-west-1',
        ],

        'sqs_itemprocessing' => [
            'driver' => 'sqs',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'prefix' => env('SQS_QUEUE_PREFIX'),
            'queue' => env('QUEUE_ITEMPROCESSING'),
            'region' => 'eu-west-1',
        ],

        'sqs_itemjobs' => [
            'driver' => 'sqs',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'prefix' => env('SQS_QUEUE_PREFIX'),
            'queue' => env('QUEUE_ITEMJOBS'),
            'region' => 'eu-west-1',
        ],

        'sqs_postprocesses' => [
            'driver' => 'sqs',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'prefix' => env('SQS_QUEUE_PREFIX'),
            'queue' => env('QUEUE_POSTPROCESSES'),
            'region' => 'eu-west-1',
        ],





    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
