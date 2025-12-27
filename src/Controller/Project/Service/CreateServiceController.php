<?php

declare(strict_types=1);

namespace App\Controller\Project\Service;

use App\Dto\Project\Schema3\ServiceConfigDto as ServiceConfigSchema3Dto;
use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Form\ServiceType;
use App\Service\Project\ProjectConfigServiceInterface;
use App\Service\Config\ServicePresetsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateServiceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
        private readonly ServicePresetsServiceInterface $servicePresetsService,
    ) {
    }

    #[Route('/project/services/new', name: 'project_services_new')]
    public function __invoke(Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(ServiceType::class, new ServiceConfigSchema3Dto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $key = $form->get('key')->getData();

                if (!isset($projectConfigDto->services[$key])) {
                    $projectConfigDto->services[$key] = $form->getData();

                    $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

                    $this->addFlash('success', 'Service created successfully!');

                    return $this->redirectToRoute('project_services');                    
                }

                $this->addFlash('danger', 'Service with this key already exists.');                
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error creating service: ' . $e->getMessage());
            }
        }

        // Get all service templates for JavaScript
        $allTemplates        = $this->servicePresetsService->getServiceTemplates();
        $templatesByCategory = [];
        foreach ($allTemplates as $key => $template) {
            $category = $template['template_category_file'] ?? 'other';

            if (!isset($templatesByCategory[$category])) {
                $templatesByCategory[$category] = [];
            }
            $templatesByCategory[$category][$key] = $template;
        }

        return $this->render('project/services/form.html.twig', [
            'page_title'  => 'Add New Service',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_services')],
                ['label' => 'Services', 'url' => $this->generateUrl('project_services')],
                ['label' => 'New', 'url' => ''],
            ],
            'form'                  => $form,
            'is_edit'               => false,
            'templates_by_category' => $templatesByCategory,
        ]);
    }
}
