<?php

declare(strict_types=1);

namespace App\Dto;

readonly class TestExecutionResultDto
{
    /**
     * @param string $testName The name of the test
     * @param string $testKey The key of the test
     * @param string $output Combined output from all commands
     * @param int $exitCode Exit code of the last command
     * @param array<string> $commands The commands that were executed
     * @param bool $success Whether the test execution was successful
     */
    public function __construct(
        public string $testName,
        public string $testKey,
        public string $output,
        public int $exitCode,
        public array $commands,
        public bool $success,
    ) {
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'testName' => $this->testName,
            'testKey' => $this->testKey,
            'output' => $this->output,
            'exitCode' => $this->exitCode,
            'commands' => $this->commands,
        ];
    }
}
