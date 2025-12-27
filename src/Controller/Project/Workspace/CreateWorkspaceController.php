<?php

declare(strict_types=1);

namespace App\Controller\Project\Workspace;

use App\Dto\Project\Schema2\WorkspaceConfigDto as WorkspaceConfigSchema2Dto;
use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Form\WorkspaceType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateWorkspaceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/workspaces/new', name: 'project_workspaces_new')]
    public function __invoke(Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(WorkspaceType::class, new WorkspaceConfigSchema2Dto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $key = $form->get('key')->getData();

                if (!isset($projectConfigDto->workspaces[$key])) {
                    $projectConfigDto->workspaces[$key] = $form->getData();

                    $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

                    $this->addFlash('success', 'Workspace created successfully!');

                    return $this->redirectToRoute('project_workspaces');
                }

                $this->addFlash('danger', 'Workspace with this key already exists.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error creating workspace: ' . $e->getMessage());
            }
        }

        return $this->render('project/workspaces/form.html.twig', [
            'page_title'  => 'Add New Workspace',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_workspaces')],
                ['label' => 'Workspaces', 'url' => $this->generateUrl('project_workspaces')],
                ['label' => 'New', 'url' => ''],
            ],
            'form'    => $form,
            'is_edit' => false,
        ]);
    }
}
