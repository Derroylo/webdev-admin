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

class EditWorkspaceController extends AbstractController
{
    public function __construct(
        private readonly WorkspaceConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/workspaces/{key}/edit', name: 'settings_workspaces_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        $workspace = $this->configService->getWorkspace($key);

        if (!$workspace) {
            $this->addFlash('danger', 'Workspace not found.');

            return $this->redirectToRoute('settings_workspaces');
        }

        $dto  = WorkspaceDto::fromArray($workspace);
        $form = $this->createForm(WorkspaceType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->configService->updateWorkspace($key, $dto->toArray());
                $this->addFlash('success', 'Workspace updated successfully!');

                return $this->redirectToRoute('settings_workspaces');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating workspace: ' . $e->getMessage());
            }
        }

        return $this->render('settings/workspaces/form.html.twig', [
            'page_title'  => 'Edit Workspace: ' . ($workspace['name'] ?? $key),
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Workspaces', 'url' => $this->generateUrl('settings_workspaces')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form'          => $form,
            'is_edit'       => true,
            'workspace_key' => $key,
        ]);
    }
}
