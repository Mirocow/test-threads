<?php

namespace tests\commands;

use tests\Commands;
use tests\TestMultithreadCommand;

/**
 * Class NoThreadsComponentCommand
 *
 * @package tests\commands
 */
class NoThreadsComponentCommand extends TestMultithreadCommand
{
    /**
     * NoThreadsComponentCommand constructor.
     *
     * @param callable $callable
     */
    public function __construct (callable $callable) {
        Commands::getInstance()->threads = 1;
        parent::__construct($callable);
    }

    /**
     * @param array $leads
     */
    public function execute(array $leads): void
    {
        while(count($leads) > 0) {
            $lead = array_shift($leads);
            call_user_func($this->callable, $lead);
        }
    }
}