<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto\Response;

use NGSOFT\Cronicle\Dto\MasterState;

/**
 * Response for get_master_state.
 */
class MasterStateResponse extends ApiResponse
{
    public ?MasterState $state = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if ('state' === $key)
        {
            return is_array($value) ? MasterState::fromArray($value) : $value;
        }

        return parent::cast($key, $value);
    }
}
