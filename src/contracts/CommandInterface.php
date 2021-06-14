<?php

namespace tests\contracts;

/**
 * Interface CommandInterface
 *
 * @package tests\contracts
 */
interface CommandInterface
{
    public function runTest(): void;

    /**
     * @return string
     */
    public function getId(): string;
}