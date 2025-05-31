<?php

namespace Logger\Exception;

use Exception;

class LoggerException extends Exception
{
    public const UNKNOWN_ERROR = 100;
    public const LOGGER_DRIVER_NOT_FOUND = 101;
    public const LOGGER_NOT_FOUND = 102;
    public const LOGGER_METHOD_NOT_FOUND = 103;

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $code = $code ?: self::UNKNOWN_ERROR;
        parent::__construct($message, $code, $previous);
    }
}
