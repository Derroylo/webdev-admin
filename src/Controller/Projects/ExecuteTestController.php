<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ExecuteTestController extends AbstractController
{
    private const CONFIG_FILE = '.devcontainer/webdev.yml';

    #[Route('/projects/tests/execute', name: 'projects_test_execute', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $projectPath = $request->request->get('projectPath');
        $testKey = $request->request->get('testKey');
        
        if (empty($projectPath) || empty($testKey)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'projectPath and testKey are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate project path
        if (empty($projectPath) || !is_dir($projectPath)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid project path',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Read webdev.yml to get test commands
        $configPath = $projectPath . '/' . self::CONFIG_FILE;
        if (!file_exists($configPath) || !is_readable($configPath)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'webdev.yml not found or not readable',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $config = Yaml::parseFile($configPath);
        } catch (ParseException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Failed to parse webdev.yml: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        // Extract test configuration
        if (!is_array($config) || !isset($config['tests']) || !is_array($config['tests'])) {
            return new JsonResponse([
                'success' => false,
                'error' => 'No tests found in webdev.yml',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($config['tests'][$testKey]) || !is_array($config['tests'][$testKey])) {
            return new JsonResponse([
                'success' => false,
                'error' => "Test '{$testKey}' not found",
            ], Response::HTTP_BAD_REQUEST);
        }

        $testConfig = $config['tests'][$testKey];
        $testName = $testConfig['name'] ?? $testKey;

        // Get commands - handle both direct commands and nested tests
        $commands = [];
        if (isset($testConfig['commands']) && is_array($testConfig['commands'])) {
            $commands = $testConfig['commands'];
        } elseif (isset($testConfig['tests']) && is_array($testConfig['tests'])) {
            // If test references other tests, we need to resolve them
            // For now, we'll just execute the commands directly
            // This could be enhanced to resolve nested test references
        }

        if (empty($commands)) {
            return new JsonResponse([
                'success' => false,
                'error' => "No commands found for test '{$testKey}'",
            ], Response::HTTP_BAD_REQUEST);
        }

        // Execute commands sequentially
        $output = '';
        $exitCode = 0;
        $allOutput = [];

        foreach ($commands as $index => $command) {
            if (empty($command) || !is_string($command)) {
                continue;
            }

            // Create process
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory($projectPath);
            $process->setTimeout(300); // 5 minutes timeout
            $process->setEnv([
                'TERM' => 'xterm-256color', // Preserve colors
            ]);

            // Execute command
            try {
                $process->run(function ($type, $buffer) use (&$allOutput, $index) {
                    if ($type === Process::OUT) {
                        $allOutput[] = [
                            'command' => $index,
                            'type' => 'stdout',
                            'data' => $buffer,
                        ];
                    } else {
                        $allOutput[] = [
                            'command' => $index,
                            'type' => 'stderr',
                            'data' => $buffer,
                        ];
                    }
                });

                $commandOutput = $process->getOutput();
                $errorOutput = $process->getErrorOutput();

                // Combine stdout and stderr
                if (!empty($commandOutput)) {
                    $output .= $commandOutput;
                }
                if (!empty($errorOutput)) {
                    $output .= $errorOutput;
                }

                // If command failed, record exit code but continue with remaining commands
                if (!$process->isSuccessful()) {
                    $exitCode = $process->getExitCode();
                    // Add separator for failed command
                    $output .= "\n[Command failed with exit code: {$exitCode}]\n";
                }
            } catch (\Exception $e) {
                $output .= "\n[Error executing command: " . $e->getMessage() . "]\n";
                $exitCode = 1;
                break; // Stop on exception
            }
        }

        return new JsonResponse([
            'success' => true,
            'testName' => $testName,
            'testKey' => $testKey,
            'output' => $output,
            'exitCode' => $exitCode,
            'commands' => $commands,
        ]);
    }
}

