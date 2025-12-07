<?php

declare(strict_types=1);

namespace App\Controller\Settings\Php;

use App\Service\Settings\Php\PhpConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewPhpSettingsController extends AbstractController
{
    public function __construct(
        private readonly PhpConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/php', name: 'settings_php')]
    public function __invoke(): Response
    {
        $phpConfig = $this->configService->getPhpConfig();

        return $this->render('settings/php/index.html.twig', [
            'page_title' => 'PHP Configuration',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'PHP Configuration', 'url' => ''],
            ],
            'config' => $phpConfig,
        ]);
    }
}