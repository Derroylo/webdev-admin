<?php

declare(strict_types=1);

namespace App\Controller\Settings\Workspaces;

use App\Service\Settings\Workspaces\WorkspaceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewWorkspacesController extends AbstractController
{
    public function __construct(
        private readonly WorkspaceConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/workspaces', name: 'settings_workspaces')]
    public function __invoke(): Response
    {
        $workspaces = $this->configService->getWorkspaces();

        return $this->render('settings/workspaces/index.html.twig', [
            'page_title'  => 'Workspaces Management',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Workspaces', 'url' => ''],
            ],
            'workspaces' => $workspaces,
        ]);
    }
}
