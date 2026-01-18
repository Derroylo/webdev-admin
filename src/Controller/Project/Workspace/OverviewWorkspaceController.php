<?php

declare(strict_types=1);

namespace App\Controller\Project\Workspace;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewWorkspaceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/workspaces', name: 'project_workspaces')]
    public function __invoke(): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        return $this->render('project/workspaces/overview.html.twig', [
            'page_title'  => 'Project Workspaces',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Workspaces', 'url' => ''],
            ],
            'workspaces' => $projectConfigDto->workspaces,
        ]);
    }
}
