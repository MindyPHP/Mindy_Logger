<?php

use Mindy\Logger\Logger;
use Mindy\Logger\Target\DummyTarget;
use Tests\TestCase;

class Psr3Test extends TestCase
{
    public function providerLevels()
    {
        return [
            ['info'],
            ['notice'],
            ['debug'],
            ['error'],
            ['warning'],
        ];
    }

    /**
     * @covers \Mindy\Logger\Logger::info
     * @covers \Mindy\Logger\Logger::notice
     * @covers \Mindy\Logger\Logger::warning
     * @covers \Mindy\Logger\Logger::error
     * @covers \Mindy\Logger\Logger::debug
     * @dataProvider providerLevels
     */
    public function testLog($level)
    {
        $logger = new Logger();
        $logger->$level('app', 'test');
        $this->assertEquals(1, count($logger->messages));
        $this->assertEquals($level, $logger->messages[0]['level']);
    }
}