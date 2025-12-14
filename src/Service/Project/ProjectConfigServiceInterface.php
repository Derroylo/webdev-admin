<?php

declare(strict_types=1);

namespace App\Service\Project;

use App\Dto\Project\Schema2\ProjectConfigDto as Schema2ProjectConfigDto;
use App\Dto\Project\Schema3\ProjectConfigDto as Schema3ProjectConfigDto;

interface ProjectConfigServiceInterface
{
    public function getProjectConfig(string $projectPath): Schema2ProjectConfigDto | Schema3ProjectConfigDto | null;
}
