<?php

declare(strict_types=1);

namespace App\Controller\Settings\NodeJs;

use App\Service\Settings\NodeJs\NodeJsConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewNodeJsSettingsController extends AbstractController
{
    public function __construct(
        private readonly NodeJsConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/nodejs', name: 'settings_nodejs')]
    public function __invoke(): Response
    {
        $nodejsConfig = $this->configService->getNodeJsConfig();

        return $this->render('settings/nodejs/index.html.twig', [
            'page_title'  => 'NodeJS Configuration',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'NodeJS Configuration', 'url' => ''],
            ],
            'config' => $nodejsConfig,
        ]);
    }
}
