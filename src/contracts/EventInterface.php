<?php

namespace tests\contracts;

Interface EventInterface
{
    /**
     * @param string   $eventName
     * @param callable $callable
     */
    public function on(string $eventName, callable $callable): void;

    /**
     * @param string $eventName
     */
    public function off(string $eventName): void;
}