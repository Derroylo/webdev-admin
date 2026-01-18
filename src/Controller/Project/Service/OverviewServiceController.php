<?php

declare(strict_types=1);

namespace App\Controller\Project\Service;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewServiceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/services', name: 'project_services')]
    public function __invoke(): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        return $this->render('project/services/overview.html.twig', [
            'page_title'  => 'Project Services',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Services', 'url' => ''],
            ],
            'services' => $projectConfigDto->services,
        ]);
    }
}
