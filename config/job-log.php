<?php

declare(strict_types=1);

return [
    /**
     * Number of days to retain job log entries. The built-in prune command
     * (php artisan job-log:prune) deletes entries older than this.
     */
    'prune_days' => (int) env('JOB_LOG_PRUNE_DAYS', 365),

    /**
     * Eloquent model used by the package. Override this with your own
     * subclass to add custom behaviour, scopes, or relationships.
     */
    'model' => CodelDev\LaravelJobLog\Models\LaravelJobLog::class,

    /**
     *  Database table used by the package to store job logs. Change this if
     * the  default conflicts with existing tables in your application.
     */
    'table' => env('JOB_LOG_TABLE', 'job_log'),
];
