<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Dto\Response;

use NGSOFT\Cronicle\Dto\Job;

/**
 * Response for get_active_jobs.
 */
class ActiveJobsResponse extends ApiResponse
{
    /** @var null|array<string, Job> keyed by job ID */
    public ?array $jobs = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if ('jobs' === $key)
        {
            if ( ! is_array($value))
            {
                return null;
            }

            $jobs = [];

            foreach ($value as $id => $row)
            {
                $job                = Job::fromArray(is_array($row) ? $row : []);
                $job->id ??= (string) $id;
                $jobs[(string) $id] = $job;
            }

            return $jobs;
        }

        return parent::cast($key, $value);
    }
}
