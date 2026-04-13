<?php

declare(strict_types=1);

use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Support\Facades\DB;

it('prunes records older than the configured retention period', function (): void
{
    LaravelJobLog::factory()
        ->create(['created_at' => now()->subDays(400)]);

    LaravelJobLog::factory()
        ->create(['created_at' => now()->subDays(400)]);

    LaravelJobLog::factory()
        ->create(['created_at' => now()->subDays(10)]);

    $this->artisan('job-log:prune')
        ->expectsOutputToContain('Pruned 2 job log entries older than 365 days')
        ->assertSuccessful();

    expect(LaravelJobLog::count())
        ->toBe(1);
});

it('respects the configured prune_days value', function (): void
{
    config()->set('job-log.prune_days', 30);

    LaravelJobLog::factory()
        ->create(['created_at' => now()->subDays(31)]);

    LaravelJobLog::factory()
        ->create(['created_at' => now()->subDays(10)]);

    $this->artisan('job-log:prune')
        ->expectsOutputToContain('older than 30 days')
        ->assertSuccessful();

    expect(LaravelJobLog::count())
        ->toBe(1);
});

it('outputs zero when no records to prune', function (): void
{
    LaravelJobLog::factory()
        ->create(['created_at' => now()]);

    $this->artisan('job-log:prune')
        ->expectsOutputToContain('Pruned 0 job log entries')
        ->assertSuccessful();

    expect(LaravelJobLog::count())
        ->toBe(1);
});

it('defaults to 365 days when config key is missing', function (): void
{
    $config = config('job-log');
    unset($config['prune_days']);
    config()->set('job-log', $config);

    LaravelJobLog::factory()
        ->create(['created_at' => now()->subDays(400)]);

    $this->artisan('job-log:prune')
        ->expectsOutputToContain('older than 365 days')
        ->assertSuccessful();

    expect(LaravelJobLog::count())
        ->toBe(0);
});

it('returns failure and reports error when an exception occurs', function (): void
{
    DB::statement('DROP TABLE IF EXISTS ' . config('job-log.table'));

    $this->artisan('job-log:prune')
        ->assertFailed();
});
