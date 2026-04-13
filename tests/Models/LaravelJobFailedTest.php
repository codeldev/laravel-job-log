<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Models\LaravelJobFailed;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;

it('uses the failed_jobs table by default', function (): void
{
    expect((new LaravelJobFailed)->getTable())
        ->toBe('failed_jobs');
});

it('uses a custom table name from queue config', function (): void
{
    config()->set('queue.failed.table', $custom = 'custom_failed_jobs');

    expect((new LaravelJobFailed)->getTable())
        ->toBe($custom);
});

it('does not use timestamps', function (): void
{
    expect((new LaravelJobFailed)->timestamps)
        ->toBeFalse();
});

it('casts failed_at to CarbonImmutable', function (): void
{
    $record = LaravelJobFailed::factory()
        ->create();

    expect($record->failed_at)
        ->toBeInstanceOf(CarbonImmutable::class);
});

it('casts id to integer', function (): void
{
    $record = LaravelJobFailed::factory()
        ->create();

    expect($record->id)
        ->toBeInt();
});

it('has a one-to-one relationship with job log', function (): void
{
    $record = LaravelJobLog::factory()
        ->failed()
        ->create();

    expect($record->failedJob->log)
        ->toBeInstanceOf(LaravelJobLog::class)
        ->and($record->failedJob->log->id)
        ->toBe($record->id);
});

it('can create records via factory', function (): void
{
    LaravelJobFailed::factory()
        ->count(3)
        ->create();

    expect(LaravelJobFailed::count())
        ->toBe(3);
});
