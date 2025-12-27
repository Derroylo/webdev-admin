<?php

declare(strict_types=1);

namespace App\Controller\Project\Service;

use App\Dto\Project\Schema3\ProjectConfigDto;
use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ToggleActiveServiceController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/project/services/{key}/toggle', name: 'project_services_toggle', methods: ['POST'])]
    public function __invoke(string $key, Request $request): Response
    {
        try {
            /** @var ProjectConfigDto $projectConfigDto */
            $projectConfigDto = $this->projectConfigService->getCurrentProjectConfig();

            $projectConfigDto->services[$key]->active = $request->request->get('active') === 'true' || $request->request->get('active') === '1';

            $this->projectConfigService->validateAndSaveCurrentProjectConfig($projectConfigDto);

            return new JsonResponse(['success' => true, 'active' => $projectConfigDto->services[$key]->active]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
