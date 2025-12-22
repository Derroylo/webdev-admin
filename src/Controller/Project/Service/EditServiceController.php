<?php

declare(strict_types=1);

namespace App\Controller\Project\Service;

use App\Dto\ServiceDto;
use App\Form\ServiceType;
use App\Service\Settings\Services\ServiceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditServiceController extends AbstractController
{
    public function __construct(
        private readonly ServiceConfigServiceInterface $configService,
    ) {
    }

    #[Route('/project/services/{key}/edit', name: 'project_services_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        $service = $this->configService->getService($key);

        if (!$service) {
            $this->addFlash('danger', 'Service not found.');

            return $this->redirectToRoute('project_services');
        }

        $dto  = ServiceDto::fromArray($service);
        $form = $this->createForm(ServiceType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->configService->updateService($key, $dto->toArray());
                $this->addFlash('success', 'Service updated successfully!');

                return $this->redirectToRoute('project_services');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating service: ' . $e->getMessage());
            }
        }

        return $this->render('project/services/form.html.twig', [
            'page_title'  => 'Edit Service: ' . $service['name'],
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
