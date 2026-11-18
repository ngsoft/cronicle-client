<?php

namespace NGSOFT\Cronicle\Dto;

/**
 * Application master state (scheduler enabled/disabled).
 */
class MasterState extends AbstractDto
{
    /** @var null|bool|int */
    public $enabled = null;

    public function isEnabled(): bool
    {
        return (bool) $this->enabled;
    }
}
