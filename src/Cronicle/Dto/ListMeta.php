<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto;

/**
 * Pagination / list metadata returned by schedule and history endpoints.
 */
class ListMeta extends AbstractDto
{
    public ?int $page_size  = null;
    public ?int $first_page = null;
    public ?int $last_page  = null;
    public ?int $length     = null;
    public ?string $type    = null;

    /**
     * @param mixed $value
     */
    protected function cast(string $key, $value): int|string
    {
        if ('type' === $key)
        {
            return (string) $value;
        }

        return (int) $value;
    }
}
