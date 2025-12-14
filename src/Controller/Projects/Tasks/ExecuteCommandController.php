<?php

declare(strict_types=1);

namespace App\Controller\Projects\Tasks;

use App\Service\Command\CommandExecutionServiceInterface;
use App\Service\Project\ProjectServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

class ExecuteCommandController extends AbstractController
{
    public function __construct(
        private readonly CommandExecutionServiceInterface $commandExecutionService,
        private readonly ProjectServiceInterface $projectService,
    ) {
    }

    #[Route('/projects/commands/execute', name: 'projects_commands_execute', methods: ['POST'])]
    public function __invoke(Request $request): StreamedResponse
    {
        $projectPath = $request->request->get('projectPath');
        $command = $request->request->get('command');

        if (empty($projectPath) || empty($command)) {
            return new StreamedResponse(function () {
                echo "data: " . json_encode([
                    'type' => 'error',
                    'message' => 'projectPath and command are required',
                ]) . "\n\n";
            }, Response::HTTP_BAD_REQUEST, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
            ]);
        }

        if (str_contains($command, 'webdev ')) {
            $project = $this->projectService->getProject($projectPath);

            if ($project !== null && $project->config !== null && $project->config->schemaVersion === 3) {
                $command = str_replace('webdev ', 'webdev-prerelease ', $command);
            }
        }

        return new StreamedResponse(function () use ($projectPath, $command) {
            try {
                // Send initial connection message
                echo "data: " . json_encode(['type' => 'start', 'command' => $command]) . "\n\n";
                ob_flush();
                flush();

                $exitCode = $this->commandExecutionService->executeCommandStreaming(
                    $command,
                    $projectPath,
                    function (string $type, string $data, int $commandIndex) {
                        // Send each chunk as it arrives
                        echo "data: " . json_encode([
                            'type' => $type,
                            'data' => $data,
                            'command' => $commandIndex,
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }
                );

                // Send completion message
                echo "data: " . json_encode([
                    'type' => 'complete',
                    'exitCode' => $exitCode,
                    'success' => $exitCode === 0,
                ]) . "\n\n";
                ob_flush();
                flush();
            } catch (\InvalidArgumentException | \RuntimeException $e) {
                echo "data: " . json_encode([
                    'type' => 'error',
                    'message' => $e->getMessage(),
                ]) . "\n\n";
                ob_flush();
                flush();
            }
        }, Response::HTTP_OK, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
