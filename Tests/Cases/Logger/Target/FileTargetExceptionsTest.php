<?php

use Mindy\Logger\Logger;
use Mindy\Logger\Target\FileTarget;
use Tests\TestCase;


class FileTargetExceptionsTest extends FileTargetTestCase
{
    /**
     * @expectedException Exception
     */
    public function testFileNotFound()
    {
        $fileTarget = new FileTarget([
            'logFile' => $this->logPath,
        ]);

        // Remove log dir
        $this->clearDir(dirname($this->logPath));

        $fileTarget->export();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgumentException()
    {
        new FileTarget();
    }
}
