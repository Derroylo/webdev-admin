<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GeneralConfigDto
{
    public bool $allowPreReleases = false;

    #[Assert\NotBlank]
    public ?string $workspaceFolder = 'workspaces';

    #[Assert\NotBlank]
    public ?string $proxyDomain = 'dev.localhost';

    #[Assert\NotBlank]
    public ?string $proxySubDomain = 'devcontainer';

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
            'proxy' => [
                'domain' => $this->proxyDomain,
                'subDomain' => $this->proxySubDomain,
            ],
        ];
    }
}
