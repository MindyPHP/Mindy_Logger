<?php

use Mindy\Logger\Logger;
use Mindy\Logger\Target\FileTarget;


class FileTargetTest extends FileTargetTestCase
{
    public function testFile()
    {
        $this->assertFileNotExists($this->logPath);

        $logger = new Logger([
            'flushInterval' => 2,
            'targets' => [
                new FileTarget([
                    'logFile' => $this->logPath
                ])
            ]
        ]);

        $logger->log(Logger::INFO, 'app', 'test');
        $logger->log(Logger::INFO, 'app', 'qwe');
        $logger->flush(true);

        $this->assertFileExists($this->logPath);
        $this->assertFileLength($this->logPath, 3); // with blank line

        $logger->log(Logger::INFO, 'app', (object)['qwe' => 'qwe']);
        $logger->flush(true);

        $this->assertFileLength($this->logPath,8);
    }

    public function testRotateLogFiles()
    {
        $logger = new Logger([
            'flushInterval' => 2,
            'targets' => [
                new FileTarget([
                    'logFile' => $this->logPath,
                    'maxFileSize' => 2
                ])
            ]
        ]);

        for ($i = 0; $i <= 1001; $i++) {
            $logger->log(Logger::INFO, 'app', 'test ' . $i);
        }
        unset($logger);

        $logFiles = $this->getLogFiles();
        $this->assertEquals(1, count($logFiles)); // rotated file not handled
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Mindy\Logger\Target\FileTarget');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * For test coverage
     */
    public function testRotateFilesReflector()
    {
        $foo = self::getMethod('rotateFiles');
        $obj = new FileTarget([
            'logFile' => $this->logPath,
        ]);
        $foo->invokeArgs($obj, []);
    }

    public function testParams()
    {
        $target = new FileTarget([
            'logFile' => $this->logPath,
            'maxLogFiles' => 0,
            'maxFileSize' => 0
        ]);
        $this->assertEquals(1, $target->maxLogFiles);
        $this->assertEquals(1, $target->maxFileSize);
    }

    /**
     * @covers \Mindy\Logger\Target\FileTarget::init
     */
    public function testCreateDir()
    {
        $this->clearDir($this->logPath);

        new FileTarget([
            'logFile' => $this->logPath,
        ]);

        $this->assertTrue(is_dir(dirname($this->logPath)));
    }

    protected function assertFileLength($filePath, $count)
    {
        $lines = explode("\n", file_get_contents($filePath));
        $this->assertEquals($count, count($lines));
    }
}
