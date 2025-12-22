<?php

declare(strict_types=1);

namespace App\Controller\Project\Secret;

use App\Service\Project\ProjectSessionServiceInterface;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecretsOverviewController extends AbstractController
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/secrets', name: 'project_secrets')]
    public function __invoke(): Response
    {
        $projectPath   = $this->projectSessionService->getCurrentProjectPath();
        $projectConfig = $this->projectConfigService->getProjectConfig($projectPath);

        return $this->render('project/secrets/overview.html.twig', [
            'page_title'  => 'Project Secrets',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Secrets', 'url' => ''],
            ],
            'secrets' => $projectConfig->secrets,
        ]);
    }
}
