<?php

namespace NGSOFT\Cronicle\Dto;

/**
 * Event timing object (cron-like schedule).
 *
 * @see https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md#event-timing-object
 */
class EventTiming extends AbstractDto
{
    /** @var null|int[] */
    public ?array $years    = null;

    /** @var null|int[] months 1-12 */
    public ?array $months   = null;

    /** @var null|int[] days 1-31 */
    public ?array $days     = null;

    /** @var null|int[] weekdays 0-6 (Sunday=0) */
    public ?array $weekdays = null;

    /** @var null|int[] hours 0-23 */
    public ?array $hours    = null;

    /** @var null|int[] minutes 0-59 */
    public ?array $minutes  = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        if (is_array($value))
        {
            return array_map('intval', $value);
        }

        return $value;
    }
}
