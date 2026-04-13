<?php

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Listeners;

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Queue\Events\JobProcessing;
use Throwable;

final class LaravelRecordJobProcessing
{
    public function handle(JobProcessing $event): void
    {
        try
        {
            LaravelJobLog::query()->updateOrCreate(
                [
                    'job_uuid' => $event->job->uuid(),
                    'attempt'  => $event->job->attempts(),
                ],
                [
                    'job'        => $event->job->resolveName(),
                    'queue'      => $event->job->getQueue(),
                    'started_at' => CarbonImmutable::now(),
                    'status'     => LaravelJobRunStatusEnum::RUNNING,
                ],
            );
        }
        catch (Throwable $throwable)
        {
            report($throwable);
        }
    }
}
