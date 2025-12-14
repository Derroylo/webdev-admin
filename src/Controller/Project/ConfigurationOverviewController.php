<?php

declare(strict_types=1);

namespace App\Controller\Project;

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
        $projectPath = $this->projectSessionService->getCurrentProjectPath();

        $projectConfig = $this->configService->getProjectConfig($projectPath);

        dump($projectConfig);
        return $this->render('project/configuration.html.twig', [
            'page_title'  => 'Project Configuration',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Configuration', 'url' => ''],
            ],
            'config' => $projectConfig,
        ]);
    }
}
