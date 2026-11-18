<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto\Response;

use NGSOFT\Cronicle\Dto\Job;
use NGSOFT\Cronicle\Dto\ListMeta;

/**
 * Response for get_event_history / get_history.
 */
class HistoryResponse extends ApiResponse
{
    /** @var null|Job[] */
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
                ? array_map(static fn ($row): Job => Job::fromArray(is_array($row) ? $row : []), $value)
                : null,
            'list'  => is_array($value) ? ListMeta::fromArray($value) : $value,
            default => parent::cast($key, $value),
        };
    }
}
