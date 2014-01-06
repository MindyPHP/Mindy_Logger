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

    public function testStringFormatMessage()
    {
        $target = new DummyTarget();
        $message = $target->formatMessage([
            'message' => [
                '{test} something {else}', [
                    'test' => 1,
                    'else' => 'qwerty'
                ]
            ]
        ]);
        $this->assertEquals('{date} [{ip}] [{level}] [{category}] 1 something qwerty', $message);
    }

    public function testObjFormatMessage()
    {
        $logger = new Logger([
            'targets' => [
                new DummyTarget()
            ]
        ]);
        $obj = new TestVars(['var1' => 1, 'var2' => 'qwerty']);
        $logger->log(Logger::INFO, 'app', $obj);

        $message = $logger->messages[0]['message'][0];
        $this->assertEquals("TestVars::__set_state(array(
   'var1' => 1,
   'var2' => 'qwerty',
   'var3' => NULL,
))", $message);
    }

    public function testArrayFormatMessage()
    {
        $logger = new Logger([
            'targets' => [
                new DummyTarget()
            ]
        ]);
        $logger->log(Logger::INFO, 'app', ['var1' => 1, 'var2' => 'qwerty']);

        $message = $logger->messages[0]['message'][0];
        $this->assertEquals("array (
  'var1' => 1,
  'var2' => 'qwerty',
)", $message);
    }

    public function testExtra()
    {
        $logger = new Logger(['extra' => true]);
        $this->assertTrue($logger->extra);
        $logger->log(Logger::INFO, 'app', 'test', [], true);

        $target = new DummyTarget();
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
