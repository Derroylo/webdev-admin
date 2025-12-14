<?php

declare(strict_types=1);

namespace App\Service\Test;

use App\Dto\Command\TestExecutionRequestDto;
use App\Dto\Command\TestExecutionResultDto;
use App\Service\Command\CommandExecutionServiceInterface;
use App\Service\Settings\Tests\TestConfigServiceInterface;

class TestExecutionService implements TestExecutionServiceInterface
{
    public function __construct(
        private readonly CommandExecutionServiceInterface $commandExecutionService,
        private readonly TestConfigServiceInterface $testConfigService,
    ) {
    }

    /**
     * Execute a test from a project's webdev.yml configuration
     */
    public function executeTest(TestExecutionRequestDto $request): TestExecutionResultDto
    {
        $this->validateProjectPath($request->projectPath);

        $testConfig = $this->testConfigService->getTestForProject($request->projectPath, $request->testKey);
        $commands = $this->extractCommands($testConfig, $request->testKey);

        $testName = $testConfig['name'] ?? $request->testKey;

        $executionResult = $this->commandExecutionService->executeCommands(
            commands: $commands,
            workingDirectory: $request->projectPath,
        );

        return new TestExecutionResultDto(
            testName: $testName,
            testKey: $request->testKey,
            output: $executionResult->output,
            exitCode: $executionResult->exitCode,
            commands: $commands,
            success: $executionResult->success,
        );
    }

    /**
     * Validate that the project path exists and is a directory
     *
     * @throws \InvalidArgumentException If the project path is invalid
     */
    private function validateProjectPath(string $projectPath): void
    {
        if (empty($projectPath) || !is_dir($projectPath)) {
            throw new \InvalidArgumentException('Invalid project path');
        }
    }

    /**
     * Extract commands from test configuration
     *
     * @param array<string, mixed> $testConfig The test configuration
     * @return array<string>
     * @throws \InvalidArgumentException If no commands are found
     */
    private function extractCommands(array $testConfig, string $testKey): array
    {
        $commands = [];

        if (isset($testConfig['commands']) && \is_array($testConfig['commands'])) {
            $commands = array_filter($testConfig['commands'], fn($cmd) => !empty($cmd) && \is_string($cmd));
        } elseif (isset($testConfig['tests']) && \is_array($testConfig['tests'])) {
            // If test references other tests, we need to resolve them
            // For now, we'll just execute the commands directly
            // This could be enhanced to resolve nested test references
        }

        if (empty($commands)) {
            throw new \InvalidArgumentException("No commands found for test '{$testKey}'");
        }

        return array_values($commands);
    }
}
