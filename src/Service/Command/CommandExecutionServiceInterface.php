<?php

declare(strict_types=1);

namespace App\Service\Command;

use App\Dto\Command\CommandExecutionResultDto;

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

    /**
     * Execute a command with streaming output
     *
     * @param string $command The command to execute
     * @param string $workingDirectory The working directory for the command
     * @param callable $onOutput Callback function that receives output chunks: function(string $type, string $data, int $commandIndex): void
     * @param int $timeout Timeout in seconds (default: 300)
     * @param array<string, string> $env Additional environment variables
     * @return int Exit code of the command
     */
    public function executeCommandStreaming(
        string $command,
        string $workingDirectory,
        callable $onOutput,
        int $timeout = 300,
        array $env = []
    ): int;
}
