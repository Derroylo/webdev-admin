<?php

declare(strict_types=1);

namespace App\Controller\Project\Task;

use App\Service\Project\ProjectSessionServiceInterface;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TasksOverviewController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/tasks', name: 'project_tasks')]
    public function __invoke(): Response
    {
        $projectPath   = $this->projectSessionService->getCurrentProjectPath();
        $projectConfig = $this->projectConfigService->getProjectConfig($projectPath);

        return $this->render('project/tasks/overview.html.twig', [
            'page_title'  => 'Project Tasks',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Tasks', 'url' => ''],
            ],
            'tasks' => $projectConfig->tasks,
        ]);
    }
}
