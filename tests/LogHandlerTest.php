<?php

use PHPUnit\Framework\TestCase;
use Logger\LogHandler;
use Logger\Log;
use Loader\Config\ConfigLoader;

class LogHandlerTest extends TestCase
{
    public function testGetInstanceWithLogDriver()
    {
        $logger = LogHandler::getInstance(Log::class);
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
        $logger = LogHandler::getInstance(Log::class);
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