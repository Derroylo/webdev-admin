<?php

declare(strict_types=1);

namespace App\Controller\Settings\Workspaces;

use App\Dto\WorkspaceDto;
use App\Form\WorkspaceType;
use App\Service\Settings\Workspaces\WorkspaceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateWorkspaceController extends AbstractController
{
    public function __construct(
        private readonly WorkspaceConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/workspaces/new', name: 'settings_workspaces_new')]
    public function __invoke(Request $request): Response
    {
        $dto = new WorkspaceDto();
        $form = $this->createForm(WorkspaceType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Generate a key from the workspace name or folder
                $key = $this->generateWorkspaceKey($request->request->all()['workspace'] ?? []);
                
                $this->configService->createWorkspace($key, $dto->toArray());
                $this->addFlash('success', 'Workspace created successfully!');

                return $this->redirectToRoute('settings_workspaces');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error creating workspace: ' . $e->getMessage());
            }
        }

        return $this->render('settings/workspaces/form.html.twig', [
            'page_title' => 'Add New Workspace',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Workspaces', 'url' => $this->generateUrl('settings_workspaces')],
                ['label' => 'New', 'url' => ''],
            ],
            'form' => $form,
            'is_edit' => false,
        ]);
    }

    private function generateWorkspaceKey(array $data): string
    {
        // Try to use folder, name, or generate unique key
        if (!empty($data['folder'])) {
            $key = $data['folder'];
        } elseif (!empty($data['name'])) {
            $key = $data['name'];
        } else {
            return 'workspace_' . uniqid();
        }
        
        // Generate a lowercase key without spaces
        $key = strtolower($key);
        $key = preg_replace('/[^a-z0-9]+/', '', $key);
        
        return $key ?: 'workspace_' . uniqid();
    }
}

