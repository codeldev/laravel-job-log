<?php

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Commands;

use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(
    'job-log:prune',
    'Prune job log entries older than the configured retention period'
)]
/** @internal */
final class LaravelJobLogCommand extends Command
{
    public function __construct(private readonly LaravelJobLog $jobLog)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        try
        {
            /** @var int $days */
            $days = config('job-log.prune_days', 365);

            /** @var int $deleted */
            $deleted = $this->jobLog::query()
                ->whereDate('created_at', '<', now()->subDays($days))
                ->delete();

            $this->info('Pruned ' . $deleted . ' job log entries older than ' . $days . ' days.');

            return self::SUCCESS;
        }
        catch (Throwable $throwable)
        {
            report($throwable);

            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
