<?php

declare(strict_types=1);

namespace App\Controller\Project\Service;

use App\Dto\ServiceDto;
use App\Form\ServiceType;
use App\Service\Config\ServicePresetsServiceInterface;
use App\Service\Settings\Services\ServiceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateServiceController extends AbstractController
{
    public function __construct(
        private readonly ServiceConfigServiceInterface $configService,
        private readonly ServicePresetsServiceInterface $servicePresetsService,
    ) {
    }

    #[Route('/project/services/new', name: 'project_services_new')]
    public function __invoke(Request $request): Response
    {
        $dto  = new ServiceDto();
        $form = $this->createForm(ServiceType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Generate a key from the service name
                $key = $this->generateServiceKey($request->request->all()['service']['name'] ?? '');

                $this->configService->createService($key, $dto->toArray());
                $this->addFlash('success', 'Service created successfully!');

                return $this->redirectToRoute('project_services');
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

    private function generateServiceKey(string $name): string
    {
        // Generate a lowercase key without spaces
        $key = strtolower($name);
        $key = preg_replace('/[^a-z0-9]+/', '', $key);
        $key = preg_replace('/^(.+?)\s*-.*$/', '$1', $name);
        $key = strtolower(preg_replace('/[^a-z0-9]+/', '', $key));

        return $key ?: 'service_' . uniqid();
    }
}
