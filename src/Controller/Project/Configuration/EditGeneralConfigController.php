<?php

declare(strict_types=1);

namespace App\Controller\Project\Configuration;

use App\Form\GeneralConfigType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditGeneralConfigController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/configuration/general/edit', name: 'project_configuration_general_edit')]
    public function __invoke(Request $request): Response
    {
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(GeneralConfigType::class, $projectConfigDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'General configuration updated successfully!');

                return $this->redirectToRoute('project_configuration');
            } catch (\Exception $e) {
                dd($e);
                $this->addFlash('danger', 'Error updating general configuration: ' . $e->getMessage());
            }
        }

        return $this->render('project/configuration/general/edit.html.twig', [
            'page_title'  => 'Edit Configuration',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Configuration', 'url' => $this->generateUrl('project_configuration')],
                ['label' => 'Edit General Configuration', 'url' => ''],
            ],
            'form' => $form,
        ]);
    }
}
