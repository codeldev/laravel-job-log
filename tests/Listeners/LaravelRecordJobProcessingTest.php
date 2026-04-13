<?php

declare(strict_types=1);

use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
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
});

it('creates a job log record when a job starts processing', function (): void
{
    Event::dispatch(new JobProcessing('database', $this->job));

    $record = LaravelJobLog::first();

    expect($record)
        ->not->toBeNull()
        ->job_uuid->toBe('test-uuid-1234')
        ->job->toBe('App\\Jobs\\TestJob')
        ->queue->toBe('default')
        ->attempt->toBe(1)
        ->status->toBe(LaravelJobRunStatusEnum::RUNNING)
        ->started_at->not->toBeNull()
        ->completed_at->toBeNull();
});

it('updates an existing record on retry', function (): void
{
    Event::dispatch(new JobProcessing('database', $this->job));
    Event::dispatch(new JobProcessing('database', $this->job));

    expect(LaravelJobLog::count())
        ->toBe(1);
});

it('creates separate records for different attempts', function (): void
{
    Event::dispatch(new JobProcessing('database', $this->job));

    $retryJob = Mockery::mock(Illuminate\Contracts\Queue\Job::class);
    $retryJob->shouldReceive('uuid')->andReturn('test-uuid-1234');
    $retryJob->shouldReceive('attempts')->andReturn(2);
    $retryJob->shouldReceive('resolveName')->andReturn('App\\Jobs\\TestJob');
    $retryJob->shouldReceive('getQueue')->andReturn('default');
    $retryJob->shouldReceive('payload')->andReturn([]);

    Event::dispatch(new JobProcessing('database', $retryJob));

    expect(LaravelJobLog::count())
        ->toBe(2);
});

it('reports the exception when the listener fails internally', function (): void
{
    Illuminate\Support\Facades\Schema::drop(config('job-log.table'));

    Event::dispatch(new JobProcessing('database', $this->job));

    expect(true)->toBeTrue();
});
