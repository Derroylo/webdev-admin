<?php

declare(strict_types=1);

namespace App\Controller\Project\Task;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Form\TaskType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditTaskController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/tasks/{key}/edit', name: 'project_tasks_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $taskDto = $projectConfigDto->tasks[$key] ?? null;

        if (!$taskDto) {
            $this->addFlash('danger', 'Task not found.');

            return $this->redirectToRoute('project_tasks');
        }

        $form = $this->createForm(TaskType::class, $taskDto);
        // The key is not mapped, so we need to set it manually
        $form->get('key')->setData($key);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $projectConfigDto->tasks[$key] = $taskDto;

                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'Task updated successfully!');

                return $this->redirectToRoute('project_tasks');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating task: ' . $e->getMessage());
            }
        }

        return $this->render('project/tasks/edit.html.twig', [
            'page_title'  => 'Edit Task: ' . $taskDto->name,
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_tasks')],
                ['label' => 'Tasks', 'url' => $this->generateUrl('project_tasks')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form'     => $form,
            'is_edit'  => true,
            'task_key' => $key,
        ]);
    }
}
