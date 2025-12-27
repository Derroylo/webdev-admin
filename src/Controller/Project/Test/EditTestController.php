<?php

declare(strict_types=1);

namespace App\Controller\Project\Test;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Dto\TestDto;
use App\Form\TestType;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditTestController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/tests/{key}/edit', name: 'project_tests_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $testDto = $projectConfigDto->tests[$key] ?? null;

        if (!$testDto) {
            $this->addFlash('danger', 'Test not found.');

            return $this->redirectToRoute('project_tests');
        }

        $form = $this->createForm(TestType::class, $testDto);
        // The key is not mapped, so we need to set it manually
        $form->get('key')->setData($key);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $projectConfigDto->tests[$key] = $testDto;

                $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);
                $this->addFlash('success', 'Test updated successfully!');

                return $this->redirectToRoute('project_tests');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating test: ' . $e->getMessage());
            }
        }

        return $this->render('project/tests/form.html.twig', [
            'page_title'  => 'Edit Test: ' . $testDto->name,
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_tests')],
                ['label' => 'Tests', 'url' => $this->generateUrl('project_tests')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form'     => $form,
            'is_edit'  => true,
            'test_key' => $key,
        ]);
    }
}
