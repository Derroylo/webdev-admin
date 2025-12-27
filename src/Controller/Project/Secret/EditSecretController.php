<?php

declare(strict_types=1);

namespace App\Controller\Project\Secret;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Dto\SecretDto;
use App\Form\SecretType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditSecretController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/secrets/{key}/edit', name: 'project_secrets_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $secretDto = $projectConfigDto->secrets[$key] ?? null;

        if (!$secretDto) {
            $this->addFlash('danger', 'Secret not found.');

            return $this->redirectToRoute('project_secrets');
        }

        $form = $this->createForm(SecretType::class, $secretDto);
        // The key is not mapped, so we need to set it manually
        $form->get('key')->setData($key);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $projectConfigDto->secrets[$key] = $secretDto;

                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'Secret updated successfully!');

                return $this->redirectToRoute('project_secrets');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating secret: ' . $e->getMessage());
            }
        }

        return $this->render('project/secrets/form.html.twig', [
            'page_title'  => 'Edit Secret: ' . $key,
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_secrets')],
                ['label' => 'Secrets', 'url' => $this->generateUrl('project_secrets')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form'       => $form,
            'is_edit'    => true,
            'secret_key' => $key,
        ]);
    }
}
