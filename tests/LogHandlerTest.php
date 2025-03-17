<?php

use Loader\Config\ConfigLoader;
use Logger\Log;
use Logger\LogHandler;
use PHPUnit\Framework\TestCase;

class LogHandlerTest extends TestCase
{
    public $config;

    public function setup(): void
    {
        $this->config = ConfigLoader::getInstance(ConfigLoader::ARRAY_LOADER);
        $this->config->set('logs', __DIR__ . '/fixture');
    }
    public function testGetInstanceWithLogDriver()
    {
        $logger = LogHandler::getInstance(Log::class, $this->config);
        $this->assertInstanceOf(Log::class, $logger);
    }

    public function testGetInstanceWithInvalidDriver()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Driver not found : InvalidDriver');
        LogHandler::getInstance('InvalidDriver');
    }

    public function testCallStaticMethod()
    {
        $logger = LogHandler::getInstance(Log::class, $this->config);
        $result = LogHandler::error('Test error message');
        $this->assertTrue($result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function testCallStaticMethodWithoutLogger()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Logger not found');
        LogHandler::error('Test error message');
    }

    public function testCallStaticMethodWithInvalidMethod()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Method invalidMethod does not exist');
        $logger = LogHandler::getInstance(Log::class);
        LogHandler::invalidMethod();
    }
}
