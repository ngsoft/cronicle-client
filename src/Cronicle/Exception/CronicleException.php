<?php

namespace NGSOFT\Cronicle\Exception;

/**
 * Thrown when a Cronicle API call fails (transport or application-level).
 */
class CronicleException extends \RuntimeException
{
    /** @var null|int|string */
    private $apiCode;

    /**
     * @param null|int|string $apiCode
     */
    public function __construct(string $message, $apiCode = null, ?\Throwable $previous = null)
    {
        $this->apiCode = $apiCode;
        parent::__construct($message, is_int($apiCode) ? $apiCode : 0, $previous);
    }

    /**
     * @return null|int|string
     */
    public function getApiCode()
    {
        return $this->apiCode;
    }
}
