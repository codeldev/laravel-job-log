<?php

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Database\Factories;

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Models\LaravelJobFailed;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JsonException;

/** @extends Factory<LaravelJobFailed> */
class LaravelJobFailedFactory extends Factory
{
    /** @var class-string<LaravelJobFailed> */
    protected $model = LaravelJobFailed::class;

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    public function definition(): array
    {
        return [
            'uuid'       => Str::uuid()->toString(),
            'connection' => 'database',
            'queue'      => 'default',
            'payload'    => json_encode(['job' => 'App\\Jobs\\' . fake()->word() . 'Job'], JSON_THROW_ON_ERROR),
            'exception'  => 'RuntimeException: ' . fake()->sentence(),
            'failed_at'  => CarbonImmutable::now(),
        ];
    }
}
