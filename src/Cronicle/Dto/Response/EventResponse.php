<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto\Response;

use NGSOFT\Cronicle\Dto\Event;
use NGSOFT\Cronicle\Dto\Job;

/**
 * Response for get_event.
 */
class EventResponse extends ApiResponse
{
    public ?Event $event = null;

    /** Number of queued jobs when Allow Queued Jobs is enabled. */
    public ?int $queue   = null;

    /** @var null|Job[] active jobs for this event */
    public ?array $jobs  = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        return match ($key)
        {
            'event' => is_array($value) ? Event::fromArray($value) : $value,
            'queue' => (int) $value,
            'jobs'  => is_array($value)
                ? array_map(static fn ($row): Job => Job::fromArray(is_array($row) ? $row : []), $value)
                : null,
            default => parent::cast($key, $value),
        };
    }
}
