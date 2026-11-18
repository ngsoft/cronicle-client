<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto;

/**
 * Job status object (active or completed).
 *
 * Extends event fields with runtime-specific properties.
 *
 * @see https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md#get_job_status
 */
class Job extends Event
{
    public ?string $hostname       = null;
    public ?string $source         = null;
    public ?string $log_file       = null;
    public ?string $command        = null;
    public ?string $event          = null;
    public ?string $event_title    = null;
    public ?string $plugin_title   = null;
    public ?string $category_title = null;
    public ?string $nice_target    = null;
    public ?string $perf           = null;
    public ?string $description    = null;

    public ?int $pid               = null;
    public ?int $now               = null;
    public ?int $log_file_size     = null;

    /** @var null|bool|int */
    public $complete               = null;

    /** Job result code (0 = success). Distinct from HTTP/API response code. */
    public int|string|null $code   = null;

    public ?float $progress        = null;
    public ?float $time_start      = null;
    public ?float $time_end        = null;
    public ?float $elapsed         = null;

    public ?ResourceStats $cpu     = null;
    public ?ResourceStats $mem     = null;

    public function isComplete(): bool
    {
        return (bool) $this->complete;
    }

    public function isSuccess(): bool
    {
        return $this->isComplete() && (0 === $this->code || '0' === $this->code);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        return match ($key)
        {
            'cpu', 'mem'                                                           => is_array($value) ? ResourceStats::fromArray($value) : $value,
            'pid', 'now', 'log_file_size'                                          => (int) $value,
            'progress', 'time_start', 'time_end', 'elapsed'                        => (float) $value,
            'hostname', 'source', 'log_file', 'command', 'event', 'event_title',
            'plugin_title', 'category_title', 'nice_target', 'perf', 'description' => (string) $value,
            default                                                                => parent::cast($key, $value),
        };
    }
}
