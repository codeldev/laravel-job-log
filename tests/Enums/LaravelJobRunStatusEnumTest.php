<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;

it('has the correct backing values', function (): void
{
    expect(LaravelJobRunStatusEnum::RUNNING->value)
        ->toBe(1)
        ->and(LaravelJobRunStatusEnum::SUCCEEDED->value)
        ->toBe(2)
        ->and(LaravelJobRunStatusEnum::FAILED->value)
        ->toBe(3);
});

it('returns human readable labels', function (): void
{
    expect(LaravelJobRunStatusEnum::RUNNING->label())
        ->toBe('Running')
        ->and(LaravelJobRunStatusEnum::SUCCEEDED->label())
        ->toBe('Succeeded')
        ->and(LaravelJobRunStatusEnum::FAILED->label())
        ->toBe('Failed');
});

it('can be created from integer values', function (): void
{
    expect(LaravelJobRunStatusEnum::from(1))
        ->toBe(LaravelJobRunStatusEnum::RUNNING)
        ->and(LaravelJobRunStatusEnum::from(2))
        ->toBe(LaravelJobRunStatusEnum::SUCCEEDED)
        ->and(LaravelJobRunStatusEnum::from(3))
        ->toBe(LaravelJobRunStatusEnum::FAILED);
});

it('has exactly three cases', function (): void
{
    expect(LaravelJobRunStatusEnum::cases())
        ->toHaveCount(3);
});
