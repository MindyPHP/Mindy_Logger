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
     * @covers \Mindy\Logger\Traits\Psr3::info
     * @covers \Mindy\Logger\Traits\Psr3::notice
     * @covers \Mindy\Logger\Traits\Psr3::warning
     * @covers \Mindy\Logger\Traits\Psr3::error
     * @covers \Mindy\Logger\Traits\Psr3::debug
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