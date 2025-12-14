<?php

declare(strict_types=1);

namespace App\Service\Command;

use App\Dto\Command\CommandExecutionResultDto;
use Symfony\Component\Process\Process;

class CommandExecutionService implements CommandExecutionServiceInterface
{
    /**
     * Execute a single shell command
     */
    public function executeCommand(
        string $command,
        string $workingDirectory,
        int $timeout = 300,
        array $env = []
    ): CommandExecutionResultDto {
        return $this->executeCommands([$command], $workingDirectory, $timeout, $env);
    }

    /**
     * Execute multiple commands sequentially
     */
    public function executeCommands(
        array $commands,
        string $workingDirectory,
        int $timeout = 300,
        array $env = []
    ): CommandExecutionResultDto {
        $output = '';
        $exitCode = 0;
        $allOutput = [];
        $allSuccessful = true;

        foreach ($commands as $index => $command) {
            if (empty($command) || !\is_string($command)) {
                continue;
            }

            $result = $this->runCommand($command, $workingDirectory, $index, $timeout, $env);

            $output .= $result['output'];
            $allOutput = array_merge($allOutput, $result['detailedOutput']);

            if (!$result['success']) {
                $exitCode = $result['exitCode'];
                $allSuccessful = false;
                $output .= "\n[Command failed with exit code: {$result['exitCode']}]\n";
            }
        }

        return new CommandExecutionResultDto(
            output: $output,
            exitCode: $exitCode,
            detailedOutput: $allOutput,
            success: $allSuccessful,
        );
    }

    /**
     * Run a single command and return its result
     *
     * @param string $command The command to execute
     * @param string $workingDirectory The working directory
     * @param int $commandIndex The index of the command in the sequence
     * @param int $timeout Timeout in seconds
     * @param array<string, string> $env Additional environment variables
     * @return array{output: string, exitCode: int, success: bool, detailedOutput: array<int, array{command: int, type: string, data: string}>}
     */
    private function runCommand(
        string $command,
        string $workingDirectory,
        int $commandIndex,
        int $timeout,
        array $env
    ): array {
        $process = Process::fromShellCommandline($command);
        $process->setWorkingDirectory($workingDirectory);
        $process->setTimeout($timeout);
        $process->setEnv(array_merge([
            'TERM' => 'xterm-256color', // Preserve colors
        ], $env));

        $detailedOutput = [];

        try {
            $process->run(function ($type, $buffer) use (&$detailedOutput, $commandIndex) {
                $detailedOutput[] = [
                    'command' => $commandIndex,
                    'type' => $type === Process::OUT ? 'stdout' : 'stderr',
                    'data' => $buffer,
                ];
            });

            $commandOutput = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            $combinedOutput = '';
            if (!empty($commandOutput)) {
                $combinedOutput .= $commandOutput;
            }
            if (!empty($errorOutput)) {
                $combinedOutput .= $errorOutput;
            }

            return [
                'output' => $combinedOutput,
                'exitCode' => $process->getExitCode() ?? 0,
                'success' => $process->isSuccessful(),
                'detailedOutput' => $detailedOutput,
            ];
        } catch (\Exception $e) {
            $detailedOutput[] = [
                'command' => $commandIndex,
                'type' => 'stderr',
                'data' => '[Error executing command: ' . $e->getMessage() . ']',
            ];

            return [
                'output' => '[Error executing command: ' . $e->getMessage() . ']',
                'exitCode' => 1,
                'success' => false,
                'detailedOutput' => $detailedOutput,
            ];
        }
    }
}
