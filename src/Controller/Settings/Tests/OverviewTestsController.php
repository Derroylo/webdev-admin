<?php

declare(strict_types=1);

namespace App\Controller\Settings\Tests;

use App\Service\Settings\Tests\TestConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewTestsController extends AbstractController
{
    public function __construct(
        private readonly TestConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/tests', name: 'settings_tests')]
    public function __invoke(): Response
    {
        $tests = $this->configService->getTests();

        return $this->render('settings/tests/index.html.twig', [
            'page_title'  => 'Tests Management',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Tests', 'url' => ''],
            ],
            'tests' => $tests,
        ]);
    }
}
