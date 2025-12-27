<?php

declare(strict_types=1);

namespace App\Controller\Project\Workspace;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteWorkspaceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/workspaces/{key}/delete', name: 'project_workspaces_delete')]
    public function __invoke(string $key): Response
    {
        try {
            /** @var ProjectConfigDto $projectConfigDto */
            $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

            unset($projectConfigDto->workspaces[$key]);

            $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

            $this->addFlash('success', 'Workspace deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting workspace: ' . $e->getMessage());
        }

        return $this->redirectToRoute('project_workspaces');
    }
}
