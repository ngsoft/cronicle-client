<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto\Response;

use NGSOFT\Cronicle\Dto\AbstractDto;

/**
 * Standard Cronicle API response envelope (code + optional description).
 */
class ApiResponse extends AbstractDto
{
    /** @var null|int|string */
    public $code                = null;

    public ?string $description = null;

    public function isSuccess(): bool
    {
        return 0 === $this->code || '0' === $this->code;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if ('description' === $key)
        {
            return (string) $value;
        }

        return $value;
    }
}
