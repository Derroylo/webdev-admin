<?php

declare(strict_types=1);

namespace App\Controller\Project\Test;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use App\Service\Project\ProjectSessionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewTestController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    #[Route('/project/tests', name: 'project_tests')]
    public function __invoke(): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        return $this->render('project/tests/overview.html.twig', [
            'page_title'  => 'Project Tests',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Tests', 'url' => ''],
            ],
            'tests' => $projectConfigDto->tests,
            'project' => $projectConfigDto,
            'project_path' => $this->projectSessionService->getCurrentProjectPath(),
        ]);
    }
}
