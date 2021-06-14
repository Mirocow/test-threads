<?php

namespace tests;

use tests\contracts\CommandInterface;

/**
 * Class TestMultithreadCommand
 *
 * @package tests
 */
abstract class TestMultithreadCommand implements CommandInterface
{
    /**
     * @var \LeadGenerator\Generator
     */
    private $generator;

    /**
     * @var callable
     */
    protected $callable;

    /**
     * TestMultithreadCommand constructor.
     *
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
        $this->generator = new \LeadGenerator\Generator();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return static::class;
    }

    public function runTest(): void
    {
        $leads = $this->getLeads();
        $this->execute($leads);
        $this->complete();
    }

    /**
     * @return array
     */
    protected function getLeads(): array
    {
        $queue = [];
        $this->generator->generateLeads(Commands::getInstance()->count, function (\LeadGenerator\Lead $lead) use (&$queue) {
            $queue[] = $lead;
        });
        return $queue;
    }

    /**
     * @param array $leads
     */
    abstract public function execute(array $leads) : void;

    protected function complete(): void
    {
        Commands::getInstance()->completeCommand($this);
    }
}