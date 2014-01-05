<?php

use Mindy\Logger\Logger;
use Mindy\Logger\Target\DummyTarget;
use Tests\TestCase;

class TestVars
{
    public $var1;

    public $var2;

    public $var3;

    public function __construct(array $options)
    {
        foreach($options as $name => $option) {
            $this->$name = $option;
        }
    }
}

class TargetTest extends TestCase
{
    public function testCustomLevel()
    {
        $target = new DummyTarget();

        $target->setLevels(['mega']);
        $this->assertEquals(['mega'], $target->getLevels());

        $target->setLevels(['info', 'debug']);
        $this->assertEquals(['info', 'debug'], $target->getLevels());
    }

    public function testFormatMessage()
    {
        $target = new DummyTarget();
        $message = $target->formatMessage('{test} something {else}', [
            'test' => 1,
            'else' => 'qwerty'
        ]);
        $this->assertEquals('1 something qwerty', $message);

        $message = $target->formatMessage(new TestVars(['var1' => 1, 'var2' => 'qwerty']));
        $this->assertEquals("TestVars::__set_state(array(
   'var1' => 1,
   'var2' => 'qwerty',
   'var3' => NULL,
))", $message);

        $message = $target->formatMessage(new TestVars(['var1' => 1, 'var2' => 'qwerty']), [1, 2, 3]);
        $this->assertEquals("TestVars::__set_state(array(
   'var1' => 1,
   'var2' => 'qwerty',
   'var3' => NULL,
))", $message);
    }

    public function testExtra()
    {
        $logger = new Logger(['extra' => true]);
        $this->assertTrue($logger->extra);
        $logger->log(Logger::INFO, 'app', 'test', [], true);

        $target = new DummyTarget(['exportInterval' => 2]);
        $target->setLevels(['info']);
        $target->categories = ['app'];

        $this->assertEquals(2, count($logger->messages));
        $target->collect($logger->messages, false);
        $this->assertEquals(2, count($target->export()));
        $this->assertNotEquals([], $target->messages);

        $target->collect($logger->messages, true);
        $this->assertEquals(0, count($target->export()));
        $this->assertEquals([], $target->messages);
    }
}
