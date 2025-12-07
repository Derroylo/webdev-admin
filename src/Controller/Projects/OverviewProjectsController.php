<?php

declare(strict_types=1);

namespace App\Controller\Projects;

use App\Service\Project\ProjectServiceInterface;
use App\Service\Config\IdePresetsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewProjectsController extends AbstractController
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
        private readonly IdePresetsServiceInterface $idePresetsService
    ) {
    }

    #[Route('/', name: 'app_dashboard')]
    #[Route('/projects/overview', name: 'projects_overview')]
    public function __invoke(Request $request): Response
    {
        $basePath = $_ENV['PROJECTS_BASE_PATH'] ?? '';

        if (empty($basePath)) {
            $this->addFlash('warning', 'PROJECTS_BASE_PATH environment variable is not set. Please configure it to scan for projects.');
            $projects = [];
        } else {
            // Check if refresh is requested
            $refresh = $request->query->getBoolean('refresh', false);
            
            if ($refresh) {
                $projects = $this->projectService->refreshProjects($basePath);
                $this->addFlash('success', 'Projects list refreshed successfully.');
            } else {
                $projects = $this->projectService->getAllProjects($basePath);
            }
        }

        return $this->render('projects/overview.html.twig', [
            'page_title' => 'Projects Overview',
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => $this->generateUrl('app_dashboard')],
                ['label' => 'Projects', 'url' => ''],
            ],
            'projects' => $projects,
            'base_path' => $basePath,
            'ideConfigs' => $this->idePresetsService->getIdeConfigs(),
        ]);
    }
}

