<?php

declare(strict_types=1);

namespace App\Controller\Project\Task;

use App\Dto\Project\Schema2\TaskConfigDto as TaskConfigSchema2Dto;
use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Form\TaskType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateTaskController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/tasks/new', name: 'project_tasks_new')]
    public function __invoke(Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(TaskType::class, new TaskConfigSchema2Dto());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $key = $form->get('key')->getData();

                if (!isset($projectConfigDto->tasks[$key])) {
                    $projectConfigDto->tasks[$key] = $form->getData();

                    $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

                    $this->addFlash('success', 'Task created successfully!');

                    return $this->redirectToRoute('project_tasks');
                }

                $this->addFlash('danger', 'Task with this key already exists.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error creating task: ' . $e->getMessage());
            }
        }

        return $this->render('project/tasks/edit.html.twig', [
            'page_title'  => 'Add New Task',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_tasks')],
                ['label' => 'Tasks', 'url' => $this->generateUrl('project_tasks')],
                ['label' => 'New', 'url' => ''],
            ],
            'form'    => $form,
            'is_edit' => false,
        ]);
    }
}
