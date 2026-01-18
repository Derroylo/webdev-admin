<?php

declare(strict_types=1);

namespace App\Controller\Project\Service;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use App\Service\Project\ProjectDockerComposeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteServiceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
        private readonly ProjectDockerComposeServiceInterface $projectDockerComposeService,
    ) {
    }

    #[Route('/project/services/{key}/delete', name: 'project_services_delete')]
    public function __invoke(string $key): Response
    {
        try {
            /** @var ProjectConfigDto $projectConfigDto */
            $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

            unset($projectConfigDto->services[$key]);

            $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

            $this->projectDockerComposeService->removeService($key);

            $this->addFlash('success', 'Service deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error deleting service: ' . $e->getMessage());
        }

        return $this->redirectToRoute('project_services');
    }
}
