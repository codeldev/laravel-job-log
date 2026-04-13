<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use CodelDev\LaravelJobLog\Models\LaravelJobFailed;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;

it('uses the table name from config', function (): void
{
    expect((new LaravelJobLog)->getTable())
        ->toBe('job_log');
});

it('uses a custom table name from config', function (): void
{
    config()
        ->set('job-log.table', $custom = 'custom_job_log');

    expect((new LaravelJobLog)->getTable())
        ->toBe($custom);
});

it('uses uuid as primary key', function (): void
{
    expect(LaravelJobLog::factory()->create()->id)
        ->toBeString()
        ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('casts status to the run status enum', function (): void
{
    $record = LaravelJobLog::factory()->create([
        'status' => LaravelJobRunStatusEnum::SUCCEEDED,
    ]);

    expect($record->status)
        ->toBe(LaravelJobRunStatusEnum::SUCCEEDED);
});

it('casts timestamps to CarbonImmutable', function (): void
{
    $record = LaravelJobLog::factory()
        ->create();

    expect($record->started_at)
        ->toBeInstanceOf(CarbonImmutable::class)
        ->and($record->completed_at)
        ->toBeInstanceOf(CarbonImmutable::class)
        ->and($record->created_at)
        ->toBeInstanceOf(CarbonImmutable::class)
        ->and($record->updated_at)
        ->toBeInstanceOf(CarbonImmutable::class);
});

it('casts duration_ms to integer', function (): void
{
    $record = LaravelJobLog::factory()
        ->create(['duration_ms' => 1500]);

    expect($record->duration_ms)
        ->toBeInt()
        ->toBe(1500);
});

it('casts attempt to integer', function (): void
{
    $record = LaravelJobLog::factory()
        ->create(['attempt' => 3]);

    expect($record->attempt)
        ->toBeInt()
        ->toBe(3);
});

it('allows nullable columns to be null', function (): void
{
    $record = LaravelJobLog::factory()->running()->create([
        'job_uuid'      => null,
        'failed_job_id' => null,
    ]);

    expect($record->fresh()->job_uuid)
        ->toBeNull()
        ->and($record->completed_at)
        ->toBeNull()
        ->and($record->duration_ms)
        ->toBeNull()
        ->and($record->failed_job_id)
        ->toBeNull();
});

it('belongs to a failed job', function (): void
{
    $record = LaravelJobLog::factory()
        ->failed()
        ->create();

    expect($record->failedJob)
        ->toBeInstanceOf(LaravelJobFailed::class);
});

it('can create records via factory', function (): void
{
    LaravelJobLog::factory()
        ->count(3)
        ->create();

    expect(LaravelJobLog::count())
        ->toBe(3);
});

it('can create running records via factory', function (): void
{
    $record = LaravelJobLog::factory()
        ->running()
        ->create();

    expect($record->status)
        ->toBe(LaravelJobRunStatusEnum::RUNNING)
        ->and($record->completed_at)
        ->toBeNull()
        ->and($record->duration_ms)
        ->toBeNull();
});

it('can create failed records via factory', function (): void
{
    $record = LaravelJobLog::factory()
        ->failed()
        ->create();

    expect($record->status)
        ->toBe(LaravelJobRunStatusEnum::FAILED)
        ->and($record->failed_job_id)
        ->not->toBeNull();
});
