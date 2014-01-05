<?php

use Mindy\Logger\Logger;
use Mindy\Logger\Target\FileTarget;
use Tests\TestCase;


class FileNotFoundTargetTest extends FileTargetTestCase
{
    public function testFileNotFound()
    {
        $fileTarget = new FileTarget([
            'logFile' => $this->logPath,
        ]);

        // Remove log dir
        $this->clearDir(dirname($this->logPath));

        try {
            $fileTarget->export();
        } catch(Exception $e) {
        }
    }
}
