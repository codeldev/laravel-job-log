<?php

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Database\Factories;

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use CodelDev\LaravelJobLog\Models\LaravelJobFailed;
use CodelDev\LaravelJobLog\Models\LaravelJobLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LaravelJobLog> */
class LaravelJobLogFactory extends Factory
{
    /** @var class-string<LaravelJobLog> */
    protected $model = LaravelJobLog::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $startedAt = CarbonImmutable::now()
            ->subMinutes(fake()->numberBetween(1, 60));

        return [
            'job_uuid'     => fake()->uuid(),
            'job'          => 'App\\Jobs\\' . fake()->word() . 'Job',
            'queue'        => 'default',
            'attempt'      => 1,
            'started_at'   => $startedAt,
            'completed_at' => $startedAt->addSeconds(fake()->numberBetween(1, 120)),
            'status'       => LaravelJobRunStatusEnum::SUCCEEDED,
            'duration_ms'  => fake()->numberBetween(100, 60000),
        ];
    }

    public function failed(): static
    {
        return $this->state(fn (): array => [
            'status'        => LaravelJobRunStatusEnum::FAILED,
            'failed_job_id' => LaravelJobFailed::factory(),
        ]);
    }

    public function running(): static
    {
        return $this->state(fn (): array => [
            'status'       => LaravelJobRunStatusEnum::RUNNING,
            'completed_at' => null,
            'duration_ms'  => null,
        ]);
    }
}
