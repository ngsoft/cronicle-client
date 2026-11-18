<?php

namespace NGSOFT\Cronicle\Dto;

/**
 * CPU or memory usage stats collected during a job.
 */
class ResourceStats extends AbstractDto
{
    public ?float $min     = null;
    public ?float $max     = null;
    public ?float $total   = null;
    public ?int $count     = null;
    public ?float $current = null;

    public function average(): ?float
    {
        if (null === $this->total || null === $this->count || $this->count < 1)
        {
            return null;
        }

        return $this->total / $this->count;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if ('count' === $key)
        {
            return (int) $value;
        }

        return (float) $value;
    }
}
