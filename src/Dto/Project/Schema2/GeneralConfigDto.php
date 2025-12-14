<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class GeneralConfigDto
{
    public bool $allowPreReleases = false;

    public string $workspaceFolder = 'workspaces';

    public string $proxyDomain = 'dev.localhost';

    public string $proxySubDomain = 'devcontainer';

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->allowPreReleases = $data['allowPreReleases'] ?? false;
        $dto->workspaceFolder = $data['workspaceFolder'] ?? 'workspaces';
        $dto->proxyDomain = $data['proxy']['domain'] ?? 'dev.localhost';
        $dto->proxySubDomain = $data['proxy']['subDomain'] ?? 'devcontainer';

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'allowPreReleases' => $this->allowPreReleases,
            'workspaceFolder' => $this->workspaceFolder,
            'proxyDomain' => $this->proxyDomain,
            'proxySubDomain' => $this->proxySubDomain,
        ];
    }
}