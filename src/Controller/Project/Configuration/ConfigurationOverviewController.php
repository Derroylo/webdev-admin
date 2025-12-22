<?php

declare(strict_types=1);

namespace App\Controller\Project\Configuration;

use App\Service\Project\ProjectConfigServiceInterface;
use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfigurationOverviewController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $configService,
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    #[Route('/project/configuration', name: 'project_configuration')]
    public function __invoke(): Response
    {
        $projectPath   = $this->projectSessionService->getCurrentProjectPath();
        $projectConfig = $this->configService->getProjectConfig($projectPath);

        return $this->render('project/configuration/overview.html.twig', [
            'page_title'  => 'Project Configuration',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Configuration', 'url' => ''],
            ],
            'config' => $projectConfig,
        ]);
    }
}
