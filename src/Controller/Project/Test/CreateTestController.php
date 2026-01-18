<?php

declare(strict_types=1);

namespace App\Controller\Project\Test;

use App\Dto\Project\Schema2\TestConfigDto as TestConfigSchema2Dto;
use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Form\TestType;
use App\Service\Project\ProjectConfigServiceInterface;
use App\Service\Config\TestPresetsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateTestController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
        private readonly TestPresetsServiceInterface $testPresetsService,
    ) {
    }

    #[Route('/project/tests/new', name: 'project_tests_new')]
    public function __invoke(Request $request): Response
    {
        /** @var ProjectConfigDto $projectConfigDto */
        $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

        $form = $this->createForm(TestType::class, new TestConfigSchema2Dto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $key = $form->get('key')->getData();

                if (!isset($projectConfigDto->tests[$key])) {
                    $projectConfigDto->tests[$key] = $form->getData();

                    $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

                    $this->addFlash('success', 'Test created successfully!');

                    return $this->redirectToRoute('project_tests');
                }

                $this->addFlash('danger', 'Test with this key already exists.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error creating test: ' . $e->getMessage());
            }
        }

        // Get all test templates for JavaScript
        $allTemplates        = $this->testPresetsService->getTestTemplates();
        $templatesByCategory = [];
        foreach ($allTemplates as $key => $template) {
            $category = $template['template_category_file'] ?? 'other';

            if (!isset($templatesByCategory[$category])) {
                $templatesByCategory[$category] = [];
            }
            $templatesByCategory[$category][$key] = $template;
        }

        return $this->render('project/tests/edit.html.twig', [
            'page_title'  => 'Add New Test',
            'breadcrumbs' => [
                ['label' => 'Project', 'url' => $this->generateUrl('project_tests')],
                ['label' => 'Tests', 'url' => $this->generateUrl('project_tests')],
                ['label' => 'New', 'url' => ''],
            ],
            'form'                  => $form,
            'is_edit'               => false,
            'templates_by_category' => $templatesByCategory,
        ]);
    }
}
