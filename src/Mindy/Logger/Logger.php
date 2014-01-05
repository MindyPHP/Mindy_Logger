<?php

namespace Mindy\Logger;

class Logger
{
    const ERROR = 'error';

    const WARNING = 'warning';

    const NOTICE = 'notice';

    const INFO = 'info';

    const DEBUG = 'debug';

    public $dateFormat = 'Y/m/d H:i:s';

    public $extra = true;

    /**
     * @var array list of the PHP predefined variables that should be logged in a message.
     * Note that a variable must be accessible via `$GLOBALS`. Otherwise it won't be logged.
     * Defaults to `['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER']`.
     */
    public $logVars = ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'];

    /**
     * @var array
     */
    public $messages = [];

    /**
     * @var array|\Mindy\Logger\Target\Target[] the log targets. Each array element represents a single [[Target|log target]] instance.
     */
    public $targets = [];

    /**
     * @var integer how many messages should be logged before they are flushed from memory and sent to targets.
     * Defaults to 1000, meaning the [[flush]] method will be invoked once every 1000 messages logged.
     * Set this property to be 0 if you don't want to flush messages until the application terminates.
     * This property mainly affects how much memory will be taken by the logged messages.
     * A smaller value means less memory, but will increase the execution time due to the overhead of [[flush()]].
     */
    public $flushInterval = 1000;

    /**
     * @var string user ip address
     */
    public $ip;

    public function __construct(array $options = [])
    {
        foreach($options as $name => $option) {
            $this->$name = $option;
        }

        $this->ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

        register_shutdown_function([$this, 'flush'], true);
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     * @return void
     */
    public function log($level, $category, $message, array $context = [], $extra = false)
    {
        if (!is_string($message)) {
            $message = var_export($message, true);
        }

        $this->messages[] = [
            'level' => $level,
            'category' => $category,
            'message' => [
                $message, $context
            ],
            'date' => date($this->dateFormat),
            'ip' => $this->ip
        ];

        if($extra) {
            $this->messages[] = $this->getExtraMessage();
        }

        if ($this->flushInterval > 0 && count($this->messages) >= $this->flushInterval) {
            $this->flush();
        }
    }

    /**
     * Flushes log messages from memory to targets.
     * @param boolean $final whether this is a final call during a request.
     */
    public function flush($final = false)
    {
        foreach($this->targets as $target) {
            $messages = $this->filterMessages($this->messages, $target->getLevels(), $target->categories, $target->except);
            $target->collect($messages, $final);
        }
        $this->messages = [];
    }

    /**
     * Generates the context information to be logged.
     * The default implementation will dump user information, system variables, etc.
     * @return string the context information. If an empty string, it means no context information.
     */
    protected function getExtraMessage()
    {
        $context = [];
        foreach ($this->logVars as $name) {
            if (!empty($GLOBALS[$name])) {
                $context[] = "\${$name} = " . var_export($GLOBALS[$name], true);
            }
        }

        return [
            'level' => self::INFO,
            'category' => 'app',
            'ip' => $this->ip,
            'message' => [
                implode("\n\n", $context), []
            ],
            'date' => date($this->dateFormat),
        ];
    }

    /**
     * Filters the given messages according to their categories and levels.
     * @param array $messages messages to be filtered
     * @param integer $levels the message levels to filter by. This is a bitmap of
     * level values. Value 0 means allowing all levels.
     * @param array $categories the message categories to filter by. If empty, it means all categories are allowed.
     * @param array $except the message categories to exclude. If empty, it means all categories are allowed.
     * @return array the filtered messages.
     */
    public function filterMessages($messages, array $levels = [], array $categories = [], array $except = [])
    {
        foreach ($messages as $i => $message) {
            if (!$this->filterLevel($message, $levels)) {
                unset($messages[$i]);
                continue;
            }

            if (!$this->filterCategories($message, $categories, $except)) {
                unset($messages[$i]);
            }
        }
        return $messages;
    }

    /**
     * @param array $message
     * @param array $levels
     * @return bool
     */
    protected function filterLevel(array $message, array $levels)
    {
        return empty($levels) || in_array($message['level'], $levels);
    }

    /**
     * @param array $message
     * @param array $categories
     * @param array $except
     * @return bool
     */
    protected function filterCategories(array $message, array $categories, array $except)
    {
        $matched = empty($categories);
        foreach ($categories as $category) {
            if ($message['category'] === $category || substr($category, -1) === '*' && (strpos($message['category'], rtrim($category, '*')) === 0 || strpos($message['category'], rtrim($category, '/*')) === 0)) {
                $matched = true;
                break;
            }
        }

        if ($matched) {
            foreach ($except as $category) {
                $prefix = rtrim($category, '*');
                if (strpos($message['category'], $prefix) === 0 && ($message['category'] === $category || $prefix !== $category)) {
                    $matched = false;
                    break;
                }
            }
        }

        return $matched;
    }
}
