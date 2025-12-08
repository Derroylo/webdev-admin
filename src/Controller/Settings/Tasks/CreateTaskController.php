<?php

declare(strict_types=1);

namespace App\Controller\Settings\Tasks;

use App\Dto\TaskDto;
use App\Form\TaskType;
use App\Service\Settings\Tasks\TaskConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateTaskController extends AbstractController
{
    public function __construct(
        private readonly TaskConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/tasks/new', name: 'settings_tasks_new')]
    public function __invoke(Request $request): Response
    {
        $dto  = new TaskDto();
        $form = $this->createForm(TaskType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Generate a key from the task name
                $key = $this->generateTaskKey($request->request->all()['task']['name'] ?? '');

                $this->configService->createTask($key, $dto->toArray());
                $this->addFlash('success', 'Task created successfully!');

                return $this->redirectToRoute('settings_tasks');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error creating task: ' . $e->getMessage());
            }
        }

        return $this->render('settings/tasks/form.html.twig', [
            'page_title'  => 'Add New Task',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Tasks', 'url' => $this->generateUrl('settings_tasks')],
                ['label' => 'New', 'url' => ''],
            ],
            'form'    => $form,
            'is_edit' => false,
        ]);
    }

    private function generateTaskKey(string $name): string
    {
        $key = strtolower($name);
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);
        $key = trim($key, '_');

        return $key ?: 'task_' . uniqid();
    }
}
