<?php

namespace NGSOFT\Cronicle\Dto\Response;

/**
 * Response for create_event.
 */
class CreateEventResponse extends ApiResponse
{
    public ?string $id = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if ('id' === $key)
        {
            return (string) $value;
        }

        return parent::cast($key, $value);
    }
}
