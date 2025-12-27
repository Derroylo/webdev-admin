<?php

declare(strict_types=1);

namespace App\Controller\Project\Configuration;

use App\Form\PhpConfigType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditPhpConfigController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/configuration/php/edit', name: 'project_configuration_php_edit')]
    public function __invoke(Request $request): Response
    {
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(PhpConfigType::class, $projectConfigDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'PHP configuration updated successfully!');

                return $this->redirectToRoute('project_configuration');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating PHP configuration: ' . $e->getMessage());
            }
        }

        return $this->render('project/configuration/php/edit.html.twig', [
            'page_title'  => 'Edit Configuration',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Configuration', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Edit Configuration', 'url' => ''],
            ],
            'form' => $form,
        ]);
    }
}
