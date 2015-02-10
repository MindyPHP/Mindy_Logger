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
 * @date 16/07/14.07.2014 12:08
 */

namespace Mindy\Logger\Formatters;

use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;
use Monolog\Formatter\LineFormatter as MonologLineFormatter;

class LineFormatter
{
    use Accessors, Configurator;

    /**
     * @var \Monolog\Formatter\LineFormatter
     */
    public $formatter;

    public $allowInlineLineBreaks = false;

    public $format = null;

    public $dateFormat = null;

    public function init()
    {
        $this->formatter = new MonologLineFormatter($this->format, $this->dateFormat, $this->allowInlineLineBreaks);
    }
}
