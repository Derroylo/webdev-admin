<?php

declare(strict_types=1);

namespace App\Controller\Settings\Tests;

use App\Service\Settings\Tests\TestConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteTestController extends AbstractController
{
    public function __construct(
        private readonly TestConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/tests/{key}/delete', name: 'settings_tests_delete')]
    public function __invoke(string $key): Response
    {
        try {
            $this->configService->deleteTest($key);
            $this->addFlash('success', 'Test deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting test: ' . $e->getMessage());
        }

        return $this->redirectToRoute('settings_tests');
    }
}
