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
 * @date 06/01/14.01.2014 01:25
 */

namespace Mindy\Logger\Traits;

/**
 * Class PSR-3.
 * For more information see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 *
 * @package Mindy\Logger\Traits
 */
trait Psr3
{
    public function info($category, $message, array $context = [], $extra = false)
    {
        /* @var $this \Mindy\Logger\Logger */
        $this->log(self::INFO, $category, $message, $context, $extra);
    }

    public function error($category, $message, array $context = [], $extra = false)
    {
        /* @var $this \Mindy\Logger\Logger */
        $this->log(self::ERROR, $category, $message, $context, $extra);
    }

    public function debug($category, $message, array $context = [], $extra = false)
    {
        /* @var $this \Mindy\Logger\Logger */
        $this->log(self::DEBUG, $category, $message, $context, $extra);
    }

    public function warning($category, $message, array $context = [], $extra = false)
    {
        /* @var $this \Mindy\Logger\Logger */
        $this->log(self::WARNING, $category, $message, $context, $extra);
    }

    public function notice($category, $message, array $context = [], $extra = false)
    {
        /* @var $this \Mindy\Logger\Logger */
        $this->log(self::NOTICE, $category, $message, $context, $extra);
    }
}
