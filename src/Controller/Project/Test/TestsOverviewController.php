<?php

declare(strict_types=1);

namespace App\Controller\Project\Test;

use App\Service\Project\ProjectSessionServiceInterface;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestsOverviewController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/tests', name: 'project_tests')]
    public function __invoke(): Response
    {
        $projectPath   = $this->projectSessionService->getCurrentProjectPath();
        $projectConfig = $this->projectConfigService->getProjectConfig($projectPath);

        return $this->render('project/tests/overview.html.twig', [
            'page_title'  => 'Project Tests',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Tests', 'url' => ''],
            ],
            'tests' => $projectConfig->tests,
        ]);
    }
}
