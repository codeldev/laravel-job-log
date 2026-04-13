<?php

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Listeners;

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Queue\Events\JobProcessed;
use Throwable;

final class LaravelRecordJobProcessed
{
    public function handle(JobProcessed $event): void
    {
        try
        {
            if (($jobLog = $this->getJobLog($event)) instanceof LaravelJobLog)
            {
                $this->updateJobLog($jobLog);
            }
        }
        catch (Throwable $throwable)
        {
            report($throwable);
        }
    }

    private function updateJobLog(LaravelJobLog $jobLog): void
    {
        $jobLog->update([
            'completed_at' => $now = CarbonImmutable::now(),
            'duration_ms'  => (int) round($now->diffInMilliseconds($jobLog->started_at, absolute: true)),
            'status'       => LaravelJobRunStatusEnum::SUCCEEDED,
        ]);
    }

    private function getJobLog(JobProcessed $event): ?LaravelJobLog
    {
        return LaravelJobLog::query()
            ->where('job_uuid', $event->job->uuid())
            ->where('attempt', $event->job->attempts())
            ->first();
    }
}
