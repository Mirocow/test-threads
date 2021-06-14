<?php

namespace tests;

use tests\contracts\EventInterface;
use tests\exceptions\EventException;

/**
 * Class EventCommand
 *
 * @package tests
 *
 * @method void beforTest(\tests\TestMultithreadCommand $command, float $start)
 * @method void afterTest(\tests\TestMultithreadCommand $command)
 */
class EventCommand implements EventInterface
{
    private $events = [];

    /**
     * @param string   $eventName
     * @param callable $callable
     */
    public function on (string $eventName, callable $callable): void
    {
        $this->events[$eventName] = $callable;
    }

    /**
     * @param string $eventName
     */
    public function off (string $eventName): void
    {
        unset($this->events[$eventName]);
    }

    /**
     * @param $name
     * @param $args
     *
     * @return false|mixed
     * @throws EventException
     */
    public function __call ($name, $args)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([&$this, $name], $args);
        }

        else if (isset($this->events[$name]) && is_callable($this->events[$name])) {
            return call_user_func_array($this->events[$name], $args);
        }

        else {
            throw new EventException();
        }
    }
}