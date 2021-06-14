<?php

namespace tests;

use tests\contracts\CommandInterface;

/**
 * Class Commands
 *
 * @package tests
 */
class Commands
{
    private static $instance;

    private $commands = [];

    /**
     * @var int Leads`s count
     */
    public $count = 100000;

    /**
     * @var int Count of threads
     */
    public $threads = 500;

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return ! (bool)count($this->commands);
    }

    /**
     * @param TestMultithreadCommand $command
     */
    public function add(TestMultithreadCommand $command): void
    {
        $this->commands[ $command->getId() ] = $command;
    }

    /**
     * @return TestMultithreadCommand[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param TestMultithreadCommand $command
     */
    public function completeCommand(TestMultithreadCommand $command): void
    {
        unset($this->commands[ $command->getId() ]);
    }

    /**
     * Public method tests for run all tests
     * 
     * @param EventCommand|null $event
     */
    public function runTests(EventCommand $event = null): void
    {
        while (!$this->isEmpty()) {
            echo "Begin tests {$this->count} leads write with threads: {$this->threads}\n";
            /** @var CommandInterface[] $commands */
            $commands = $this->getCommands();
            foreach ($commands as $command) {
                if($event) {
                    $params = $event->beforeTest($command);
                }
                $command->runTest($this->threads);
                if($event) {
                    $event->afterTest($command, $params);
                }
            }
        }
    }

    /**
     * @return Commands
     */
    public static function getInstance(): Commands
    {
        if (!static::$instance) {
            static::$instance = new Commands();
        }

        return static::$instance;
    }
}