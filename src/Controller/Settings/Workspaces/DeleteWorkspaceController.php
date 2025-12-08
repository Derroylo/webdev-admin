<?php

declare(strict_types=1);

namespace App\Controller\Settings\Workspaces;

use App\Service\Settings\Workspaces\WorkspaceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteWorkspaceController extends AbstractController
{
    public function __construct(
        private readonly WorkspaceConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/workspaces/{key}/delete', name: 'settings_workspaces_delete')]
    public function __invoke(string $key): Response
    {
        try {
            $this->configService->deleteWorkspace($key);
            $this->addFlash('success', 'Workspace deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting workspace: ' . $e->getMessage());
        }

        return $this->redirectToRoute('settings_workspaces');
    }
}
