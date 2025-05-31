<?php

use Loader\Config\ConfigLoader;
use Logger\Log;
use Logger\Logger;
use PHPUnit\Framework\TestCase;

class LogHandlerTest extends TestCase
{
    public $config;

    public function setup(): void
    {
        $this->config = ConfigLoader::getInstance(ConfigLoader::ARRAY_LOADER);
        $this->config->set('logs', __DIR__ . '/fixture');
    }

    public function tearDown(): void
    {
        $pattern = __DIR__ . '/fixture/*.log';
        $files = glob($pattern);

        foreach ($files as $file) {
            if (is_file($file)) { // Check if it's a file
                unlink($file); // Delete the file
            }
        }
    }
    public function testGetInstanceWithLogDriver()
    {
        $logger = Logger::getInstance(Log::class, 'ALL', $this->config);
        $this->assertInstanceOf(Log::class, $logger);
    }

    public function testGetInstanceWithInvalidDriver()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Driver not found : InvalidDriver');
        Logger::getInstance('InvalidDriver');
    }

    public function testCallStaticMethod()
    {
        Logger::getInstance(Log::class, 'ALL', $this->config);
        $result = Logger::error('Test error message');
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
        Logger::error('Test error message');
    }

    public function testCallStaticMethodWithInvalidMethod()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Method invalidMethod does not exist');
        Logger::getInstance(Log::class, 'ALL', $this->config);
        Logger::invalidMethod();
    }
}
