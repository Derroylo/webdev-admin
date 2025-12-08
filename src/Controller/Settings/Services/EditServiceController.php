<?php

declare(strict_types=1);

namespace App\Controller\Settings\Services;

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

    #[Route('/settings/services/{key}/edit', name: 'settings_services_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        $service = $this->configService->getService($key);

        if (!$service) {
            $this->addFlash('danger', 'Service not found.');

            return $this->redirectToRoute('settings_services');
        }

        $dto  = ServiceDto::fromArray($service);
        $form = $this->createForm(ServiceType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->configService->updateService($key, $dto->toArray());
                $this->addFlash('success', 'Service updated successfully!');

                return $this->redirectToRoute('settings_services');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating service: ' . $e->getMessage());
            }
        }

        return $this->render('settings/services/form.html.twig', [
            'page_title'  => 'Edit Service: ' . $service['name'],
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Services', 'url' => $this->generateUrl('settings_services')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form'        => $form,
            'is_edit'     => true,
            'service_key' => $key,
        ]);
    }
}
