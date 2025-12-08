<?php

namespace App\Controller\Settings;

use App\Service\Settings\NodeJs\NodeJsConfigServiceInterface;
use App\Service\Settings\Php\PhpConfigServiceInterface;
use App\Service\Settings\Secrets\SecretConfigServiceInterface;
use App\Service\Settings\Services\ServiceConfigServiceInterface;
use App\Service\Settings\Tasks\TaskConfigServiceInterface;
use App\Service\Settings\Tests\TestConfigServiceInterface;
use App\Service\Settings\Workspaces\WorkspaceConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly PhpConfigServiceInterface $phpConfigService,
        private readonly NodeJsConfigServiceInterface $nodejsConfigService,
        private readonly ServiceConfigServiceInterface $serviceConfigService,
        private readonly TaskConfigServiceInterface $taskConfigService,
        private readonly TestConfigServiceInterface $testConfigService,
        private readonly WorkspaceConfigServiceInterface $workspaceConfigService,
        private readonly SecretConfigServiceInterface $secretConfigService,
    ) {
    }

    #[Route('', name: 'settings_dashboard')]
    public function index(): Response
    {
        $phpConfig    = $this->phpConfigService->getPhpConfig();
        $nodejsConfig = $this->nodejsConfigService->getNodeJsConfig();
        $services     = $this->serviceConfigService->getServices();
        $tasks        = $this->taskConfigService->getTasks();
        $tests        = $this->testConfigService->getTests();
        $workspaces   = $this->workspaceConfigService->getWorkspaces();
        $secrets      = $this->secretConfigService->getSecrets();

        // Count active services
        $activeServices = array_filter($services, fn ($service) => $service['active'] ?? false);

        return $this->render('settings/dashboard.html.twig', [
            'page_title'  => 'Settings Dashboard',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => ''],
            ],
            'php_version'      => $phpConfig['version'] ?? 'N/A',
            'nodejs_version'   => $nodejsConfig['version'] ?? 'N/A',
            'total_services'   => \count($services),
            'active_services'  => \count($activeServices),
            'total_tasks'      => \count($tasks),
            'total_tests'      => \count($tests),
            'total_workspaces' => \count($workspaces),
            'total_secrets'    => \count($secrets),
        ]);
    }
}
