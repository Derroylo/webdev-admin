<?php

declare(strict_types=1);

namespace App\Controller\Settings\Secrets;

use App\Service\Settings\Secrets\SecretConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteSecretController extends AbstractController
{
    public function __construct(
        private readonly SecretConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/secrets/{key}/delete', name: 'settings_secrets_delete')]
    public function __invoke(string $key): Response
    {
        try {
            $this->configService->deleteSecret($key);
            $this->addFlash('success', 'Secret deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting secret: ' . $e->getMessage());
        }

        return $this->redirectToRoute('settings_secrets');
    }
}

