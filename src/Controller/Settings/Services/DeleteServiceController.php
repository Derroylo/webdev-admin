<?php

declare(strict_types=1);

namespace App\Controller\Settings\Services;

use App\Service\Settings\Services\ServiceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteServiceController extends AbstractController
{
    public function __construct(
        private readonly ServiceConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/services/{key}/delete', name: 'settings_services_delete')]
    public function __invoke(string $key): Response
    {
        try {
            $this->configService->deleteService($key);
            $this->addFlash('success', 'Service deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting service: ' . $e->getMessage());
        }

        return $this->redirectToRoute('settings_services');
    }
}
