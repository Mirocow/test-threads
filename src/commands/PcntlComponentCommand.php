<?php

namespace tests\commands;

use LeadGenerator\Generator;
use tests\Commands;
use tests\exceptions\AsyncException;
use tests\TestMultithreadCommand;

/**
 * Class PcntlComponentCommand
 *
 * @package tests\commands
 */
class PcntlComponentCommand extends TestMultithreadCommand
{
    private $leads = [];

    /**
     * @param array $leads
     *
     * @throws AsyncException
     */
    public function execute(array $leads): void
    {
       $this->leads = $leads;
        while(count($this->leads) > 0) {
            $this->runAsync(Commands::getInstance()->threads);
        }
    }

    /**
     * @param $forks
     *
     * @throws AsyncException
     */
    public function runAsync($forks)
    {
        $lead = array_shift($this->leads);
        $pid   = pcntl_fork();
        if ($pid == -1) {
            throw new AsyncException();
        }
        else if ($pid) {
            $forks -= 1;
            if ($forks > 0) {
                $this->runAsync($forks);
            }
            pcntl_wait($status);
        }
        else {
            call_user_func($this->callable, $lead);
            posix_kill(getmypid(), SIGKILL);
        }
    }
}