<?php

declare(strict_types=1);

namespace App\Controller\Settings\Tasks;

use App\Service\Settings\Tasks\TaskConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OverviewTasksController extends AbstractController
{
    public function __construct(
        private readonly TaskConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/tasks', name: 'settings_tasks')]
    public function __invoke(): Response
    {
        $tasks = $this->configService->getTasks();

        return $this->render('settings/tasks/index.html.twig', [
            'page_title'  => 'Tasks Management',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Tasks', 'url' => ''],
            ],
            'tasks' => $tasks,
        ]);
    }
}
