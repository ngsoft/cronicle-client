<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto\Response;

use NGSOFT\Cronicle\Dto\Job;

/**
 * Response for get_job_status.
 */
class JobStatusResponse extends ApiResponse
{
    public ?Job $job = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if ('job' === $key)
        {
            return is_array($value) ? Job::fromArray($value) : $value;
        }

        return parent::cast($key, $value);
    }
}
