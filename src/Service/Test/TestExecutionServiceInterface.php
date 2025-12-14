<?php

declare(strict_types=1);

namespace App\Service\Test;

use App\Dto\Command\TestExecutionRequestDto;
use App\Dto\Command\TestExecutionResultDto;

interface TestExecutionServiceInterface
{
    /**
     * Execute a test from a project's webdev.yml configuration
     *
     * @param TestExecutionRequestDto $request The test execution request
     * @return TestExecutionResultDto The test execution result
     * @throws \InvalidArgumentException If the project path is invalid or test not found
     * @throws \RuntimeException If the configuration file cannot be read or parsed
     */
    public function executeTest(TestExecutionRequestDto $request): TestExecutionResultDto;
}
