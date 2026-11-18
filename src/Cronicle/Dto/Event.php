<?php

namespace NGSOFT\Cronicle\Dto;

/**
 * Cronicle event data object.
 *
 * @see https://github.com/jhuckaby/Cronicle/blob/master/docs/APIReference.md#event-data-format
 */
class Event extends AbstractDto
{
    public ?string $id             = null;
    public ?string $title          = null;
    public ?string $category       = null;
    public ?string $plugin         = null;
    public ?string $target         = null;
    public ?string $algo           = null;
    public ?string $api_key        = null;
    public ?string $timezone       = null;
    public ?string $username       = null;
    public ?string $notes          = null;
    public ?string $notify_success = null;
    public ?string $notify_fail    = null;
    public ?string $web_hook       = null;
    public ?string $chain          = null;
    public ?string $chain_error    = null;

    /** @var null|bool|int */
    public $enabled                = null;

    /** @var null|bool|int */
    public $catch_up               = null;

    /** @var null|bool|int */
    public $detached               = null;

    /** @var null|bool|int */
    public $multiplex              = null;

    /** @var null|bool|int */
    public $queue                  = null;

    public ?int $created           = null;
    public ?int $modified          = null;
    public ?int $timeout           = null;
    public ?int $retries           = null;
    public ?int $retry_delay       = null;
    public ?int $max_children      = null;
    public ?int $stagger           = null;
    public ?int $queue_max         = null;
    public ?int $cpu_limit         = null;
    public ?int $cpu_sustain       = null;
    public ?int $memory_limit      = null;
    public ?int $memory_sustain    = null;
    public ?int $log_max_size      = null;

    /** @var null|array<string, mixed> */
    public ?array $params          = null;

    public ?EventTiming $timing    = null;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cast(string $key, $value)
    {
        return match ($key)
        {
            'timing'                         => is_array($value) ? EventTiming::fromArray($value) : $value,
            'params'                         => is_array($value) ? $value : null,
            'created', 'modified', 'timeout', 'retries', 'retry_delay', 'max_children',
            'stagger', 'queue_max', 'cpu_limit', 'cpu_sustain', 'memory_limit',
            'memory_sustain', 'log_max_size' => (int) $value,
            'id', 'title', 'category', 'plugin', 'target', 'algo', 'api_key', 'timezone',
            'username', 'notes', 'notify_success', 'notify_fail', 'web_hook', 'chain',
            'chain_error'                    => (string) $value,
            default                          => $value,
        };
    }
}
