<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Dto\Project\AbstractProjectConfigDto;

interface ProjectConfigServiceInterface
{
    public function getCurrentProjectConfig(): AbstractProjectConfigDto | null;

    public function getProjectConfig(string $projectPath): AbstractProjectConfigDto | null;

    public function validateAndSaveCurrentProjectConfig(AbstractProjectConfigDto $projectConfigDto): void;

    public function validateAndSaveProjectConfig(AbstractProjectConfigDto $projectConfigDto, string $projectPath): void;
}
