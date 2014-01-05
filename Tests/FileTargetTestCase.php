<?php
use Tests\TestCase;

/**
 *
 *
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 06/01/14.01.2014 02:02
 */

class FileTargetTestCase extends TestCase
{
    /**
     * @var string
     */
    public $logPath;

    public function setUp()
    {
        $this->logPath = __DIR__ . '/../../../Logs/app.log';
        @mkdir(dirname($this->logPath));
    }

    public function tearDown()
    {
        $this->clearLogs();
    }

    protected function clearLogs()
    {
        foreach($this->getLogFiles() as $file) {
            @unlink($file);
        }
    }

    protected function clearDir($dir)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
    }

    protected function getLogFiles()
    {
        return glob(realpath(dirname($this->logPath)) . '/app.log*');
    }
}
