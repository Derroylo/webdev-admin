<?php

declare(strict_types=1);

namespace App\Controller\Project\Service;

use App\Service\Project\ProjectConfigServiceInterface;
use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServicesOverviewController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/services', name: 'project_services')]
    public function __invoke(): Response
    {
        $projectPath   = $this->projectSessionService->getCurrentProjectPath();
        $projectConfig = $this->projectConfigService->getProjectConfig($projectPath);

        return $this->render('project/services/overview.html.twig', [
            'page_title'  => 'Project Services',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Services', 'url' => ''],
            ],
            'services' => $projectConfig->services,
        ]);
    }
}
