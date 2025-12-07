<?php

declare(strict_types=1);

namespace App\Controller\Settings\Tests;

use App\Dto\TestDto;
use App\Form\TestType;
use App\Service\Settings\Tests\TestConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditTestController extends AbstractController
{
    public function __construct(
        private readonly TestConfigServiceInterface $configService
    ) {
    }

    #[Route('/settings/tests/edit', name: 'settings_tests_edit')]
    public function __invoke(string $key, Request $request): Response
    {
        $test = $this->configService->getTest($key);
        
        if (!$test) {
            $this->addFlash('danger', 'Test not found.');
            return $this->redirectToRoute('settings_tests');
        }

        $dto = TestDto::fromArray($test);
        $form = $this->createForm(TestType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->configService->updateTest($key, $dto->toArray());
                $this->addFlash('success', 'Test updated successfully!');

                return $this->redirectToRoute('settings_tests');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error updating test: ' . $e->getMessage());
            }
        }

        return $this->render('settings/tests/form.html.twig', [
            'page_title' => 'Edit Test: ' . $test['name'],
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => $this->generateUrl('settings_dashboard')],
                ['label' => 'Tests', 'url' => $this->generateUrl('settings_tests')],
                ['label' => 'Edit: ' . $key, 'url' => ''],
            ],
            'form' => $form,
            'is_edit' => true,
            'test_key' => $key,
        ]); 
    }
}
