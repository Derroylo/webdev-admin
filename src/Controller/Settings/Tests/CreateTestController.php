<?php

declare(strict_types=1);

namespace App\Controller\Settings\Tests;

use App\Dto\TestDto;
use App\Form\TestType;
use App\Service\Config\TestPresetsServiceInterface;
use App\Service\Settings\Tests\TestConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateTestController extends AbstractController
{
    public function __construct(
        private readonly TestConfigServiceInterface $configService,
        private readonly TestPresetsServiceInterface $testPresetsService,
    ) {
    }

    #[Route('/settings/tests/new', name: 'settings_tests_new')]
    public function __invoke(Request $request): Response
    {
        $dto = new TestDto();
        // Initialize with one empty command field
        $dto->commands = [''];

        $form = $this->createForm(TestType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Generate a key from the test name
                $key = $this->generateTestKey($request->request->all()['test']['name'] ?? '');

                $this->configService->createTest($key, $dto->toArray());
                $this->addFlash('success', 'Test created successfully!');

                return $this->redirectToRoute('settings_tests');
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

        return $this->render('settings/tests/form.html.twig', [
            'page_title'  => 'Add New Test',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Tests', 'url' => $this->generateUrl('settings_tests')],
                ['label' => 'New', 'url' => ''],
            ],
            'form'                  => $form,
            'is_edit'               => false,
            'templates_by_category' => $templatesByCategory,
        ]);
    }

    private function generateTestKey(string $name): string
    {
        $key = strtolower($name);
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);
        $key = trim($key, '_');

        return $key ?: 'test_' . uniqid();
    }
}
