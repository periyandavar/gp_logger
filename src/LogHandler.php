<?php

namespace Logger;

use Loader\Config\ConfigLoader;

class LogHandler
{
    protected static $logger;

    public static function getInstance(string $driver, $level = 'ALL', ?ConfigLoader $config = null)
    {
        switch ($driver) {
            case Log::class:
                self::$logger = Log::getInstance($level, $config);
                break;
            
            default:
                throw new \Exception('Driver not found : ' . $driver);
        }

        return self::$logger;
    }

    
    public static function __callStatic($name, $arguments)
    {
        if (self::$logger === null) {
            throw new \Exception("Logger not found");
        }
        if (self::$logger->$name === null) {
            throw new \Exception("Method $name does not exist");
        }

        return self::$logger->$name(...$arguments);
    }
}