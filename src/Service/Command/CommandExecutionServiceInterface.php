<?php

declare(strict_types=1);

namespace App\Service\Command;

use App\Dto\CommandExecutionResultDto;

interface CommandExecutionServiceInterface
{
    /**
     * Execute a single shell command
     *
     * @param string $command The command to execute
     * @param string $workingDirectory The working directory for the command
     * @param int $timeout Timeout in seconds (default: 300)
     * @param array<string, string> $env Additional environment variables
     * @return CommandExecutionResultDto The execution result
     */
    public function executeCommand(
        string $command,
        string $workingDirectory,
        int $timeout = 300,
        array $env = []
    ): CommandExecutionResultDto;

    /**
     * Execute multiple commands sequentially
     *
     * @param array<string> $commands Array of commands to execute
     * @param string $workingDirectory The working directory for the commands
     * @param int $timeout Timeout in seconds per command (default: 300)
     * @param array<string, string> $env Additional environment variables
     * @return CommandExecutionResultDto The combined execution result
     */
    public function executeCommands(
        array $commands,
        string $workingDirectory,
        int $timeout = 300,
        array $env = []
    ): CommandExecutionResultDto;
}
