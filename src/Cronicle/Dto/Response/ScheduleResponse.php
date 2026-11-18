<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto\Response;

use NGSOFT\Cronicle\Dto\Event;
use NGSOFT\Cronicle\Dto\ListMeta;

/**
 * Response for get_schedule.
 */
class ScheduleResponse extends ApiResponse
{
    /** @var null|Event[] */
    public ?array $rows    = null;

    public ?ListMeta $list = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        return match ($key)
        {
            'rows'  => is_array($value)
                ? array_map(static fn ($row): Event => Event::fromArray(is_array($row) ? $row : []), $value)
                : null,
            'list'  => is_array($value) ? ListMeta::fromArray($value) : $value,
            default => parent::cast($key, $value),
        };
    }
}
