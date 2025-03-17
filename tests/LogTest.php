<?php

use Loader\Config\ConfigLoader;
use Logger\Log;
use PHPUnit\Framework\TestCase;
use PSpell\Config;

class LogTest extends TestCase
{
    public $config;

    public function setup(): void
    {
        $this->config = ConfigLoader::getInstance(ConfigLoader::ARRAY_LOADER);
        $this->config->set('logs', __DIR__ . '/fixture');
    }
    public function testGetInstance()
    {
        $log = Log::getInstance('ALL', $this->config);
        $this->assertInstanceOf(Log::class, $log);
    }

    public function testError()
    {
        $log = Log::getInstance('ERROR', $this->config);
        $result = $log->error('This is an error message');
        $this->assertTrue($result);
    }

    public function testInfo()
    {
        $log = Log::getInstance('INFO', $this->config);
        $result = $log->info('This is an info message');
        $this->assertTrue($result);
    }

    public function testWarning()
    {
        $log = Log::getInstance('WARNING', $this->config);
        $result = $log->warning('This is a warning message');
        $this->assertTrue($result);
    }

    public function testFatal()
    {
        $log = Log::getInstance('FATAL', $this->config);
        $result = $log->fatal('This is a fatal message');
        $this->assertTrue($result);
    }

    public function testDebug()
    {
        $log = Log::getInstance('DEBUG', $this->config);
        $result = $log->debug('This is a debug message');
        $this->assertTrue($result);
    }

    public function testActivity()
    {
        $log = Log::getInstance('ALL', $this->config);
        $result = $log->activity('This is an activity message');
        $this->assertTrue($result);
    }

    public function testCustom()
    {
        $log = Log::getInstance('ALL', $this->config);
        $result = $log->custom('custom.log', 'This is a custom message');
        $this->assertTrue($result);
    }
}
