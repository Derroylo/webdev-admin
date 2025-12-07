<?php

declare(strict_types=1);

namespace App\Controller\Settings\Services;

use App\Service\Settings\Services\ServiceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewServicesController extends AbstractController
{
    public function __construct(
        private readonly ServiceConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/services', name: 'settings_services')]
    public function __invoke(): Response
    {
        $services = $this->configService->getServices();

        return $this->render('settings/services/index.html.twig', [
            'page_title' => 'Services Management',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Services', 'url' => ''],
            ],
            'services' => $services,
        ]);
    }
}