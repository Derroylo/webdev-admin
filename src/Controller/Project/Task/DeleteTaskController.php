<?php

declare(strict_types=1);

namespace App\Controller\Project\Task;

use App\Service\Settings\Tasks\TaskConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteTaskController extends AbstractController
{
    public function __construct(
        private readonly TaskConfigServiceInterface $configService,
    ) {
    }

    #[Route('/project/tasks/{key}/delete', name: 'project_tasks_delete')]
    public function __invoke(string $key): Response
    {
        try {
            $this->configService->deleteTask($key);
            $this->addFlash('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting task: ' . $e->getMessage());
        }

        return $this->redirectToRoute('project_tasks');
    }
}
