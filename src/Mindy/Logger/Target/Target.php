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
 * @date 05/01/14.01.2014 16:10
 */

namespace Mindy\Logger\Target;


abstract class Target
{
    /**
     * @var array list of message categories that this target is interested in. Defaults to empty, meaning all categories.
     * You can use an asterisk at the end of a category so that the category may be used to
     * match those categories sharing the same common prefix. For example, 'yii\db\*' will match
     * categories starting with 'yii\db\', such as 'yii\db\Connection'.
     */
    public $categories = [];

    /**
     * @var integer how many messages should be accumulated before they are exported.
     * Defaults to 1000. Note that messages will always be exported when the application terminates.
     * Set this property to be 0 if you don't want to export messages until the application terminates.
     */
    public $exportInterval = 1000;

    public $except = [];

    public $messages = [];

    private $_levels = [];

    public function __construct(array $options = [])
    {
        foreach($options as $name => $option) {
            $this->$name = $option;
        }

        $this->init();
    }

    public function init()
    {

    }

    /**
     * @return array levels
     */
    public function getLevels()
    {
        return $this->_levels;
    }

    public function setLevels(array $levels = [])
    {
        $this->_levels = $levels;
    }

    /**
     * Processes the given log messages.
     * This method will filter the given messages with [[levels]] and [[categories]].
     * And if requested, it will also export the filtering result to specific medium (e.g. email).
     * @param array $messages log messages to be processed. See [[Logger::messages]] for the structure
     * of each message.
     * @param boolean $final whether this method is called at the end of the current application
     */
    public function collect(array $messages, $final)
    {
        $this->messages = array_merge($this->messages, $messages);
        $count = count($this->messages);
        if ($count > 0 && ($final || $this->exportInterval > 0 && $count >= $this->exportInterval)) {
            $this->export();
            $this->messages = [];
        }
    }

    public function formatData(array $context)
    {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        return $replace;
    }

    public function formatMessage(array $data)
    {
        $format = '{date} [{ip}] [{level}] [{category}] {message}';
        list($message, $context) = $data['message'];
        unset($data['message']);

        $logMessage = strtr($message, $this->formatData($context));
        return strtr($format, array_merge($this->formatData($data), [
            '{message}' => $logMessage
        ]));
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    abstract public function export();
}
