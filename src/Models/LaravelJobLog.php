<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Models;

use Carbon\CarbonImmutable;
use CodelDev\LaravelJobLog\Database\Factories\LaravelJobLogFactory;
use CodelDev\LaravelJobLog\Enums\LaravelJobRunStatusEnum;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property-read string $id
 * @property-read string|null $job_uuid
 * @property-read string $job
 * @property-read string $queue
 * @property-read int $attempt
 * @property-read CarbonImmutable $started_at
 * @property-read CarbonImmutable|null $completed_at
 * @property-read LaravelJobRunStatusEnum $status
 * @property-read int|null $duration_ms
 * @property-read int|null $failed_job_id
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read LaravelJobFailed|null $failedJob
 */
#[UseFactory(LaravelJobLogFactory::class)]
final class LaravelJobLog extends Model
{
    /** @use HasFactory<LaravelJobLogFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var list<string> */
    protected $guarded = [];

    #[Override]
    public function getTable(): string
    {
        /** @var string */
        return config('job-log.table', 'job_log');
    }

    /** @return BelongsTo<LaravelJobFailed, $this> */
    public function failedJob(): BelongsTo
    {
        return $this->belongsTo(LaravelJobFailed::class, 'failed_job_id', 'id');
    }

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'id'            => 'string',
            'job_uuid'      => 'string',
            'job'           => 'string',
            'queue'         => 'string',
            'attempt'       => 'integer',
            'started_at'    => 'immutable_datetime',
            'completed_at'  => 'immutable_datetime',
            'status'        => LaravelJobRunStatusEnum::class,
            'duration_ms'   => 'integer',
            'failed_job_id' => 'integer',
            'created_at'    => 'immutable_datetime',
            'updated_at'    => 'immutable_datetime',
        ];
    }
}
