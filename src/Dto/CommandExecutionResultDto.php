<?php

declare(strict_types=1);

namespace App\Dto;

readonly class CommandExecutionResultDto
{
    /**
     * @param string $output Combined stdout and stderr output
     * @param int $exitCode Exit code of the last command (0 for success)
     * @param array<int, array{command: int, type: string, data: string}> $detailedOutput Detailed output per command
     * @param bool $success Whether all commands executed successfully
     */
    public function __construct(
        public string $output,
        public int $exitCode,
        public array $detailedOutput,
        public bool $success,
    ) {
    }

    public function toArray(): array
    {
        return [
            'output' => $this->output,
            'exitCode' => $this->exitCode,
            'detailedOutput' => $this->detailedOutput,
            'success' => $this->success,
        ];
    }
}
