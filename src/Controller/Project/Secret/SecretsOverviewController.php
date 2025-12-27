<?php

declare(strict_types=1);

namespace App\Controller\Project\Secret;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecretsOverviewController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/secrets', name: 'project_secrets')]
    public function __invoke(): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        return $this->render('project/secrets/overview.html.twig', [
            'page_title'  => 'Project Secrets',
            'breadcrumbs' => [
                ['label' => 'Projects', 'url' => $this->generateUrl('projects_overview')],
                ['label' => 'Project Secrets', 'url' => ''],
            ],
            'secrets' => $projectConfigDto->secrets,
        ]);
    }
}
