<?php

declare(strict_types=1);

namespace App\Controller\Settings\Services;

use App\Service\Settings\Services\ServiceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ToggleActiveServiceController extends AbstractController
{
    public function __construct(
        private readonly ServiceConfigServiceInterface $configService,
    ) {
    }

    #[Route('/toggle/{key}', name: 'settings_services_toggle', methods: ['POST'])]
    public function __invoke(string $key, Request $request): Response
    {
        try {
            $active = $request->request->get('active') === 'true' || $request->request->get('active') === '1';
            $this->configService->toggleService($key, $active);

            return new JsonResponse(['success' => true, 'active' => $active]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
