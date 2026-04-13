<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Models;

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Database\Factories\LaravelJobFailedFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Override;

/**
 * This model uses the default auto-incrementing integer primary key.
 * The `uuid` column is a unique identifier but not the primary key.
 *
 * @property-read int $id
 * @property-read string $uuid
 * @property-read string $connection
 * @property-read string $queue
 * @property-read string $payload
 * @property-read string $exception
 * @property-read CarbonImmutable $failed_at
 * @property-read LaravelJobLog|null $log
 */
#[UseFactory(LaravelJobFailedFactory::class)]
final class LaravelJobFailed extends Model
{
    /** @use HasFactory<LaravelJobFailedFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    #[Override]
    public function getTable(): string
    {
        /** @var string */
        return config('queue.failed.table', 'failed_jobs');
    }

    /** @return HasOne<LaravelJobLog, $this> */
    public function log(): HasOne
    {
        return $this->hasOne(LaravelJobLog::class, 'failed_job_id', 'id');
    }

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'id'         => 'integer',
            'uuid'       => 'string',
            'connection' => 'string',
            'queue'      => 'string',
            'payload'    => 'string',
            'exception'  => 'string',
            'failed_at'  => 'immutable_datetime',
        ];
    }
}
