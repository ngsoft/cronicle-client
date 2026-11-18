<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto\Response;

/**
 * Response for run_event.
 */
class RunEventResponse extends ApiResponse
{
    /** @var null|string[] launched job IDs */
    public ?array $ids = null;

    /** Number of queued jobs when Allow Queued Jobs is enabled. */
    public ?int $queue = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        return match ($key)
        {
            'ids'   => is_array($value) ? array_map(strval(...), $value) : null,
            'queue' => (int) $value,
            default => parent::cast($key, $value),
        };
    }
}
