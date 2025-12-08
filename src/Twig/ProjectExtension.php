<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Project\ProjectSessionServiceInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class ProjectExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ProjectSessionServiceInterface $projectSessionService,
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'project_selected'     => $this->projectSessionService->hasProjectSelected(),
            'current_project_path' => $this->projectSessionService->getCurrentProjectPath(),
            'current_project_name' => $this->projectSessionService->getProjectName(),
        ];
    }
}
