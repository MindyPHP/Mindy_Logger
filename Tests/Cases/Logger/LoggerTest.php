<?php

use Mindy\Logger\Logger;
use Mindy\Logger\Target\DummyTarget;
use Tests\TestCase;

class LoggerTest extends TestCase
{
    public function testLog()
    {
        $logger = new Logger();
        $this->assertEquals('127.0.0.1', $logger->ip);
        $logger->log(Logger::INFO, 'app', 'test');

        $this->assertEquals(1, count($logger->messages));
    }

    public function testFilterMessages()
    {
        $logger = new Logger();

        $logger->log(Logger::INFO, 'app', 'test');
        $logger->log(Logger::INFO, 'custom', 'test');
        $logger->log(Logger::INFO, 'app/something', 'test');
        $logger->log('custom', 'app', 'test');

        $filtered = $logger->filterMessages($logger->messages, ['info'], ['app']);
        $this->assertEquals(1, count($filtered));
    }

    public function testFilterExcept()
    {
        $logger = new Logger();

        $logger->log(Logger::INFO, 'app', 'test');
        $logger->log(Logger::INFO, 'app/other', 'test');
        $logger->log(Logger::INFO, 'app/something', 'test');
        $logger->log('custom', 'app/something', 'test');

        $filtered = $logger->filterMessages($logger->messages, ['info'], ['app/*'], ['app/something']);
        $this->assertEquals(2, count($filtered));
    }

    public function testTargets()
    {
        $logger = new Logger([
            'targets' => [
                new DummyTarget(),
                new DummyTarget(),
                new DummyTarget(),
            ]
        ]);
        $this->assertInstanceOf('Mindy\Logger\Target\Target', $logger->targets[0]);
        $this->assertInstanceOf('Mindy\Logger\Target\Target', $logger->targets[1]);
        $this->assertInstanceOf('Mindy\Logger\Target\DummyTarget', $logger->targets[2]);
    }

    public function testFlush1()
    {
        $logger = new Logger([
            'flushInterval' => 1,
            'targets' => [new DummyTarget()]
        ]);
        $logger->log(Logger::INFO, 'app', 'test');
        $this->assertEquals([], $logger->messages);
        $logger->log(Logger::INFO, 'app', 'test');
        $this->assertEquals([], $logger->messages);

        $target = $logger->targets[0];
        $data = $target->export();
        $this->assertEquals(2, count($data));
    }

    public function testFlush2()
    {
        $logger = new Logger([
            'flushInterval' => 2,
            'targets' => [new DummyTarget()]
        ]);
        $logger->log(Logger::INFO, 'app', 'test');
        $this->assertEquals(1, count($logger->messages));
        $logger->log(Logger::INFO, 'app', 'test');
        $this->assertEquals(0, count($logger->messages));

        $target = $logger->targets[0];
        $data = $target->export();
        $this->assertEquals(2, count($data));
    }

    public function testFlush100()
    {
        $logger = new Logger([
            'flushInterval' => 100,
            'targets' => [new DummyTarget()]
        ]);
        $logger->log(Logger::INFO, 'app', 'test');
        $logger->log(Logger::INFO, 'app', 'test');
        $logger->log(Logger::INFO, 'app', 'test');
        $logger->log(Logger::INFO, 'app', 'test');
        $logger->log(Logger::INFO, 'app', 'test');
        $this->assertEquals(5, count($logger->messages));

        $target = $logger->targets[0];
        $data = $target->export();
        $this->assertEquals(0, count($data));

        $logger->flush();
        $target = $logger->targets[0];
        $data = $target->export();
        $this->assertEquals(5, count($data));
    }
}