<?php

declare(strict_types=1);

namespace App\Controller\Settings\Secrets;

use App\Service\Settings\Secrets\SecretConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewSecretsController extends AbstractController
{
    public function __construct(
        private readonly SecretConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/secrets', name: 'settings_secrets')]
    public function __invoke(): Response
    {
        $secrets = $this->configService->getSecrets();

        return $this->render('settings/secrets/index.html.twig', [
            'page_title'  => 'Secrets Management',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Secrets', 'url' => ''],
            ],
            'secrets' => $secrets,
        ]);
    }
}
