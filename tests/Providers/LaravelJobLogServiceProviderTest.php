<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use CodelDev\LaravelJobLog\Commands\LaravelJobLogCommand;
use CodelDev\LaravelJobLog\Listeners\LaravelRecordJobFailed;
use CodelDev\LaravelJobLog\Listeners\LaravelRecordJobProcessed;
use CodelDev\LaravelJobLog\Listeners\LaravelRecordJobProcessing;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

it('registers the config file', function (): void
{
    expect(config('job-log.table'))
        ->toBe('job_log')
        ->and(config('job-log.prune_days'))
        ->toBe(365)
        ->and(config('job-log.model'))
        ->toBe(LaravelJobLog::class);
});

it('registers the prune command', function (): void
{
    expect($commands = Artisan::all())
        ->toHaveKey('job-log:prune')
        ->and($commands['job-log:prune'])
        ->toBeInstanceOf(LaravelJobLogCommand::class);
});

it('binds the model from config', function (): void
{
    expect(app(LaravelJobLog::class))
        ->toBeInstanceOf(LaravelJobLog::class);
});

it('binds a custom model when configured', function (): void
{
    expect(app()->bound(LaravelJobLog::class))
        ->toBeTrue();
});

it('registers the JobProcessing listener', function (): void
{
    Event::fake([JobProcessing::class]);

    Event::assertListening(
        JobProcessing::class,
        LaravelRecordJobProcessing::class,
    );
});

it('registers the JobProcessed listener', function (): void
{
    Event::fake([JobProcessed::class]);

    Event::assertListening(
        JobProcessed::class,
        LaravelRecordJobProcessed::class,
    );
});

it('registers the JobFailed listener', function (): void
{
    Event::fake([JobFailed::class]);

    Event::assertListening(
        JobFailed::class,
        LaravelRecordJobFailed::class,
    );
});
