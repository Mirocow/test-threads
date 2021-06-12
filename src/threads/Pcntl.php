<?php

namespace tests;

class ThreadsByPcntl
{
    private $queue;

    public function __construct(&$queue)
    {
        $this->queue = &$queue;
        $this->forks = [];
    }

    public function runAsync ($forks)
    {
        $lead = array_shift($this->queue);
        $pid   = pcntl_fork();
        if ($pid == -1) {
            die('could not fork');
        }
        else if ($pid) {
            $forks -= 1;
            if ($forks > 0) {
                $this->runAsync($forks);
            }
            pcntl_wait($status);
        }
        else {
            asyncCall($lead);
            posix_kill(getmypid(), SIGKILL);
        }
    }

}