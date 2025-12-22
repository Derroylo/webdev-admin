<?php

declare(strict_types=1);

namespace App\Controller\Project\Workspace;

use App\Service\Project\ProjectSessionServiceInterface;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorkspacesOverviewController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/workspaces', name: 'project_workspaces')]
    public function __invoke(): Response
    {
        $projectPath   = $this->projectSessionService->getCurrentProjectPath();
        $projectConfig = $this->projectConfigService->getProjectConfig($projectPath);

        return $this->render('project/workspaces/overview.html.twig', [
            'page_title'  => 'Project Workspaces',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Workspaces', 'url' => ''],
            ],
            'workspaces' => $projectConfig->workspaces,
        ]);
    }
}
