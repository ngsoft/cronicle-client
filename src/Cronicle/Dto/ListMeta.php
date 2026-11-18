<?php

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
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if ('type' === $key)
        {
            return (string) $value;
        }

        return (int) $value;
    }
}
