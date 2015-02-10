<?php
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
 * @date 10/09/14.09.2014 22:43
 */

namespace Mindy\Logger\Handler;

use Monolog\Handler\RotatingFileHandler as MonoRotatingFileHandler;

class RotatingFileHandler extends StreamHandler
{
    /**
     * @var int The maximal amount of files to keep (0 means unlimited)
     */
    public $maxFiles = 10;

    public function getHandler()
    {
        return new MonoRotatingFileHandler(
            $this->stream,
            $this->maxFiles,
            $this->getLevel(),
            $this->bubble,
            $this->filePermission
        );
    }
}
