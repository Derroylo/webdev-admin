<?php

declare(strict_types=1);

namespace App\Controller\Project\Task;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteTaskController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/tasks/{key}/delete', name: 'project_tasks_delete')]
    public function __invoke(string $key): Response
    {
        try {
            /** @var ProjectConfigDto $projectConfigDto */
            $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

            unset($projectConfigDto->tasks[$key]);

            $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

            $this->addFlash('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting task: ' . $e->getMessage());
        }

        return $this->redirectToRoute('project_tasks');
    }
}
