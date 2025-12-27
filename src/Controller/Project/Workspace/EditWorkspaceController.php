<?php

declare(strict_types=1);

namespace App\Controller\Project\Workspace;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Dto\WorkspaceDto;
use App\Form\WorkspaceType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditWorkspaceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/workspaces/{key}/edit', name: 'project_workspaces_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $workspaceDto = $projectConfigDto->workspaces[$key] ?? null;

        if (!$workspaceDto) {
            $this->addFlash('danger', 'Workspace not found.');

            return $this->redirectToRoute('project_workspaces');
        }

        $form = $this->createForm(WorkspaceType::class, $workspaceDto);
        // The key is not mapped, so we need to set it manually
        $form->get('key')->setData($key);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $projectConfigDto->workspaces[$key] = $workspaceDto;

                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'Workspace updated successfully!');

                return $this->redirectToRoute('project_workspaces');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating workspace: ' . $e->getMessage());
            }
        }

        return $this->render('project/workspaces/form.html.twig', [
            'page_title'  => 'Edit Workspace: ' . $workspaceDto->name,
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_workspaces')],
                ['label' => 'Workspaces', 'url' => $this->generateUrl('project_workspaces')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form'          => $form,
            'is_edit'       => true,
            'workspace_key' => $key,
        ]);
    }
}
