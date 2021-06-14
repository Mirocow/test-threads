<?php

namespace tests\commands;

use tests\Commands;
use tests\TestMultithreadCommand;
use Swoole\Process;

/**
 * Class SwooleComponentCommand
 *
 * @package tests\commands
 */
class SwooleComponentCommand extends TestMultithreadCommand
{
    /**
     * @param array $leads
     */
    public function execute(array $leads): void
    {
        while (count($leads) > 0) {
            for ($i = 0; $i < Commands::getInstance()->threads; $i++) {
                $lead    = array_shift($leads);
                $process = new \Swoole\Process([$this, 'asyncCall']);
                $process->write(serialize($lead));
                $pid           = $process->start();
                $workers[$pid] = $process;
            }
        }
    }

    public function asyncCall(\Swoole\Process $process){
        $lead = unserialize($process->read(), $array = []);
        call_user_func($this->callable, $lead);
    }
}