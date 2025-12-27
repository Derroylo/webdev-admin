<?php

declare(strict_types=1);

namespace App\Controller\Project\Service;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Form\ServiceType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditServiceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/services/{key}/edit', name: 'project_services_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $serviceDto = $projectConfigDto->services[$key] ?? null;

        if (!$serviceDto) {
            $this->addFlash('danger', 'Service not found.');

            return $this->redirectToRoute('project_services');
        }

        $form = $this->createForm(ServiceType::class, $serviceDto);
        // The key is not mapped, so we need to set it manually
        $form->get('key')->setData($key);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $projectConfigDto->services[$key] = $serviceDto;

                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'Service updated successfully!');

                return $this->redirectToRoute('project_services');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating service: ' . $e->getMessage());
            }
        }

        return $this->render('project/services/form.html.twig', [
            'page_title'  => 'Edit Service: ' . $serviceDto->name,
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_services')],
                ['label' => 'Services', 'url' => $this->generateUrl('project_services')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form'        => $form,
            'is_edit'     => true,
            'service_key' => $key,
        ]);
    }
}
