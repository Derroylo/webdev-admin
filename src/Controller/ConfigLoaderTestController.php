<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Project\ProjectConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfigLoaderTestController extends AbstractController
{
    public function __construct(
        private readonly ProjectConfigServiceInterface $projectConfigService,
    ) {
    }

    #[Route('/config/loader/test', name: 'config_loader_test')]
    public function __invoke(): Response
    {
        dump($this->projectConfigService->getProjectConfig('/home/carsten/projects/kundenportal'));
        dump($this->projectConfigService->getProjectConfig('/home/carsten/projects/berner-safety.de-oxid'));

        die("test");
    }
}