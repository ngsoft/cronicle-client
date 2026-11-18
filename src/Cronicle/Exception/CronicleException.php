<?php

declare(strict_types=1);

namespace NGSOFT\Cronicle\Exception;

/**
 * Thrown when a Cronicle API call fails (transport or application-level).
 */
class CronicleException extends \RuntimeException
{
    /**
     * @param null|int|string $apiCode
     */
    public function __construct(string $message, private $apiCode = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, is_int($this->apiCode) ? $this->apiCode : 0, $previous);
    }

    /**
     * @return null|int|string
     */
    public function getApiCode()
    {
        return $this->apiCode;
    }
}
