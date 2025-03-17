<?php

use Logger\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testGetInstance()
    {
        $log = Log::getInstance();
        $this->assertInstanceOf(Log::class, $log);
    }

    public function testError()
    {
        $log = Log::getInstance('ERROR');
        $result = $log->error('This is an error message');
        $this->assertTrue($result);
    }

    public function testInfo()
    {
        $log = Log::getInstance('INFO');
        $result = $log->info('This is an info message');
        $this->assertTrue($result);
    }

    public function testWarning()
    {
        $log = Log::getInstance('WARNING');
        $result = $log->warning('This is a warning message');
        $this->assertTrue($result);
    }

    public function testFatal()
    {
        $log = Log::getInstance('FATAL');
        $result = $log->fatal('This is a fatal message');
        $this->assertTrue($result);
    }

    public function testDebug()
    {
        $log = Log::getInstance('DEBUG');
        $result = $log->debug('This is a debug message');
        $this->assertTrue($result);
    }

    public function testActivity()
    {
        $log = Log::getInstance('ALL');
        $result = $log->activity('This is an activity message');
        $this->assertTrue($result);
    }

    public function testCustom()
    {
        $log = Log::getInstance('ALL');
        $result = $log->custom('custom.log', 'This is a custom message');
        $this->assertTrue($result);
    }
}
