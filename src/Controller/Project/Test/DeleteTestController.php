<?php

declare(strict_types=1);

namespace App\Controller\Project\Test;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteTestController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/tests/{key}/delete', name: 'project_tests_delete')]
    public function __invoke(string $key): Response
    {
        try {
            /** @var ProjectConfigDto $projectConfigDto */
            $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

            unset($projectConfigDto->tests[$key]);

            $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

            $this->addFlash('success', 'Test deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting test: ' . $e->getMessage());
        }

        return $this->redirectToRoute('project_tests');
    }
}
