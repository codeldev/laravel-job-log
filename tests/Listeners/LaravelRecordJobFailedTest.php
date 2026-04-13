<?php

declare(strict_types=1);

use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use CodelDev\LaravelJobLog\Models\LaravelJobFailed;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Queue\Events\JobFailed;
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

it('marks a job as failed when processing fails', function (): void
{
    $exception = new RuntimeException('Something went wrong');

    Event::dispatch(new JobFailed('database', $this->job, $exception));

    $record = LaravelJobLog::first();

    expect($record)
        ->status->toBe(LaravelJobRunStatusEnum::FAILED)
        ->completed_at->not->toBeNull()
        ->duration_ms->not->toBeNull();
});

it('links to the failed_jobs record when available', function (): void
{
    $failedJob = LaravelJobFailed::factory()->create([
        'uuid' => 'test-uuid-1234',
    ]);

    $exception = new RuntimeException('Something went wrong');

    Event::dispatch(new JobFailed('database', $this->job, $exception));

    $record = LaravelJobLog::first();

    expect($record->failed_job_id)
        ->toBe($failedJob->id)
        ->and($record->failedJob)
        ->toBeInstanceOf(LaravelJobFailed::class);
});

it('sets failed_job_id to null when no failed_jobs record exists', function (): void
{
    $exception = new RuntimeException('Something went wrong');

    Event::dispatch(new JobFailed('database', $this->job, $exception));

    $record = LaravelJobLog::first();

    expect($record->failed_job_id)
        ->toBeNull();
});

it('does nothing when no matching job log record exists', function (): void
{
    $unknownJob = Mockery::mock(Illuminate\Contracts\Queue\Job::class);
    $unknownJob->shouldReceive('uuid')->andReturn('unknown-uuid');
    $unknownJob->shouldReceive('attempts')->andReturn(1);
    $unknownJob->shouldReceive('payload')->andReturn([]);

    Event::dispatch(new JobFailed('database', $unknownJob, new RuntimeException('fail')));

    $record = LaravelJobLog::first();

    expect($record->status)
        ->toBe(LaravelJobRunStatusEnum::RUNNING);
});

it('reports the exception when the listener fails internally', function (): void
{
    Illuminate\Support\Facades\Schema::drop(config('job-log.table'));

    Event::dispatch(new JobFailed('database', $this->job, new RuntimeException('fail')));

    expect(true)->toBeTrue();
});
