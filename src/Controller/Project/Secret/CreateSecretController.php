<?php

declare(strict_types=1);

namespace App\Controller\Project\Secret;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Dto\Project\Schema2\SecretConfigDto as SecretConfigSchema2Dto;
use App\Form\SecretType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateSecretController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/secrets/new', name: 'project_secrets_new')]
    public function __invoke(Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(SecretType::class, new SecretConfigSchema2Dto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Generate a key from the source key
                $key = $form->get('key')->getData();

                if (!isset($projectConfigDto->secrets[$key])) {
                    $projectConfigDto->secrets[$key] = $form->getData();

                    $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

                    $this->addFlash('success', 'Secret created successfully!');

                    return $this->redirectToRoute('project_secrets');
                }

                $this->addFlash('danger', 'Secret with this key already exists.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error creating secret: ' . $e->getMessage());
            }
        }

        return $this->render('project/secrets/edit.html.twig', [
            'page_title'  => 'Add New Secret',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_secrets')],
                ['label' => 'Secrets', 'url' => $this->generateUrl('project_secrets')],
                ['label' => 'New', 'url' => ''],
            ],
            'form'    => $form,
            'is_edit' => false,
        ]);
    }
}
