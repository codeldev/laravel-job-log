<?php

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Listeners;

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use CodelDev\LaravelJobLog\Models\LaravelJobFailed;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Queue\Events\JobFailed;
use Throwable;

final class LaravelRecordJobFailed
{
    public function handle(JobFailed $event): void
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

    private function getJobLog(JobFailed $event): ?LaravelJobLog
    {
        return LaravelJobLog::query()
            ->where('job_uuid', $event->job->uuid())
            ->where('attempt', $event->job->attempts())
            ->first();
    }

    private function updateJobLog(LaravelJobLog $jobLog): void
    {
        $jobLog->update([
            'completed_at'  => $now = CarbonImmutable::now(),
            'duration_ms'   => (int) round($now->diffInMilliseconds($jobLog->started_at, absolute: true)),
            'status'        => LaravelJobRunStatusEnum::FAILED,
            'failed_job_id' => $jobLog->job_uuid === null ? null : $this->getFailedJobId($jobLog->job_uuid),
        ]);
    }

    private function getFailedJobId(string $uuid): ?int
    {
        /** @var int|null */
        return LaravelJobFailed::query()
            ->where('uuid', $uuid)
            ->value('id');
    }
}
