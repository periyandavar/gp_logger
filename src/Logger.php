<?php

namespace Logger;

use Loader\Config\ConfigLoader;
use Logger\Exception\LoggerException;

class Logger
{
    /**
     * Logger
     */
    protected static $logger;

    /**
     * Return the logger instance.
     *
     * @param  string     $driver
     * @param  mixed      $level
     * @param  ?ConfigLoader      $config
     * @throws \Exception
     * @return Log
     */
    public static function getInstance(string $driver, $level = 'ALL', ?ConfigLoader $config = null)
    {
        switch ($driver) {
            case Log::class:
                self::$logger = Log::getInstance($level, $config);
                break;

            default:
                throw new LoggerException('Driver not found : ' . $driver, LoggerException::LOGGER_DRIVER_NOT_FOUND);
        }

        return self::$logger;
    }

    /**
     * Handle the logger methods.
     *
     * @param  string                            $name
     * @param  mixed                             $arguments
     * @throws \Logger\Exception\LoggerException
     */
    public static function __callStatic(string $name, $arguments)
    {
        if (self::$logger === null) {
            throw new LoggerException('Logger not found', LoggerException::LOGGER_NOT_FOUND);
        }
        if (!method_exists(self::$logger, $name)) {
            throw new LoggerException("Method $name does not exist", LoggerException::LOGGER_METHOD_NOT_FOUND);
        }

        return self::$logger->$name(...$arguments);
    }
}
