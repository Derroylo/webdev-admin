<?php

declare(strict_types=1);

namespace App\Controller\Settings\General;

use App\Service\Settings\General\GeneralConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewGeneralConfigController extends AbstractController
{
    public function __construct(
        private readonly GeneralConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/general', name: 'settings_general')]
    public function __invoke(): Response
    {
        $generalConfig = $this->configService->getGeneralConfig();

        return $this->render('settings/general/index.html.twig', [
            'page_title'  => 'General Configuration',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'General Configuration', 'url' => ''],
            ],
            'config' => $generalConfig,
        ]);
    }
}
