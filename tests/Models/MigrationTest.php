<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('creates the job_log table with all expected columns', function (string $column): void
{
    expect(Schema::hasColumn(config('job-log.table'), $column))
        ->toBeTrue();
})->with([
    'id',
    'job_uuid',
    'job',
    'queue',
    'attempt',
    'started_at',
    'completed_at',
    'status',
    'duration_ms',
    'failed_job_id',
    'created_at',
    'updated_at',
]);
