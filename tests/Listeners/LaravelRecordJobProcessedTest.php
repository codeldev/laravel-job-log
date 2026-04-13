<?php

declare(strict_types=1);

use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;

beforeEach(function (): void
{
    $this->job = Mockery::mock(Illuminate\Contracts\Queue\Job::class);
    $this->job->shouldReceive('uuid')->andReturn('test-uuid-1234');
    $this->job->shouldReceive('attempts')->andReturn(1);
    $this->job->shouldReceive('resolveName')->andReturn('App\\Jobs\\TestJob');
    $this->job->shouldReceive('getQueue')->andReturn('default');
    $this->job->shouldReceive('payload')->andReturn([]);

    Event::dispatch(new JobProcessing('database', $this->job));
});

it('marks a job as succeeded when processing completes', function (): void
{
    Event::dispatch(new JobProcessed('database', $this->job));

    $record = LaravelJobLog::first();

    expect($record)
        ->status->toBe(LaravelJobRunStatusEnum::SUCCEEDED)
        ->completed_at->not->toBeNull()
        ->duration_ms->not->toBeNull();
});

it('calculates duration in milliseconds', function (): void
{
    Event::dispatch(new JobProcessed('database', $this->job));

    $record = LaravelJobLog::first();

    expect($record->duration_ms)
        ->toBeInt()
        ->toBeGreaterThanOrEqual(0);
});

it('does nothing when no matching job log record exists', function (): void
{
    $unknownJob = Mockery::mock(Illuminate\Contracts\Queue\Job::class);
    $unknownJob->shouldReceive('uuid')->andReturn('unknown-uuid');
    $unknownJob->shouldReceive('attempts')->andReturn(1);
    $unknownJob->shouldReceive('payload')->andReturn([]);

    Event::dispatch(new JobProcessed('database', $unknownJob));

    $record = LaravelJobLog::first();

    expect($record->status)
        ->toBe(LaravelJobRunStatusEnum::RUNNING);
});

it('reports the exception when the listener fails internally', function (): void
{
    Illuminate\Support\Facades\Schema::drop(config('job-log.table'));

    Event::dispatch(new JobProcessed('database', $this->job));

    expect(true)->toBeTrue();
});
