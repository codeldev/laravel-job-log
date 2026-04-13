<?php

declare(strict_types=1);

namespace CodelDev\LaravelJobLog;

use CodelDev\LaravelJobLog\Commands\LaravelJobLogCommand;
use CodelDev\LaravelJobLog\Listeners\LaravelRecordJobFailed;
use CodelDev\LaravelJobLog\Listeners\LaravelRecordJobProcessed;
use CodelDev\LaravelJobLog\Listeners\LaravelRecordJobProcessing;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelJobLogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-job-log')
            ->hasConfigFile()
            ->hasMigration('create_job_log_table')
            ->hasCommand(LaravelJobLogCommand::class);
    }

    public function bootingPackage(): void
    {
        $this->bindModels();
        $this->registerListeners();
    }

    private function registerListeners(): void
    {
        Event::listen(JobProcessing::class, LaravelRecordJobProcessing::class);
        Event::listen(JobProcessed::class, LaravelRecordJobProcessed::class);
        Event::listen(JobFailed::class, LaravelRecordJobFailed::class);
    }

    private function bindModels(): void
    {
        /** @var class-string $model */
        $model = config('job-log.model', LaravelJobLog::class);

        $this->app->bind(LaravelJobLog::class, $model);
    }
}
