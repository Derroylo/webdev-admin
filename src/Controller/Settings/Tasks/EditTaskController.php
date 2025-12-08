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

class EditTaskController extends AbstractController
{
    public function __construct(
        private readonly TaskConfigServiceInterface $configService,
    ) {
    }

    #[Route('/settings/tasks/edit', name: 'settings_tasks_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        $task = $this->configService->getTask($key);

        if (!$task) {
            $this->addFlash('danger', 'Task not found.');

            return $this->redirectToRoute('settings_tasks');
        }

        $dto  = TaskDto::fromArray($task);
        $form = $this->createForm(TaskType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->configService->updateTask($key, $dto->toArray());
                $this->addFlash('success', 'Task updated successfully!');

                return $this->redirectToRoute('settings_tasks');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating task: ' . $e->getMessage());
            }
        }

        return $this->render('settings/tasks/form.html.twig', [
            'page_title'  => 'Edit Task: ' . $task['name'],
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Tasks', 'url' => $this->generateUrl('settings_tasks')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form'     => $form,
            'is_edit'  => true,
            'task_key' => $key,
        ]);
    }
}
