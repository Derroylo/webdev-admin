<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class PhpConfigDto
{
    public string $version = '';

    /** @var array<string, mixed> */
    public array $config = [];

    /** @var array<string, mixed> */
    public array $configWeb = [];

    /** @var array<string, mixed> */
    public array $configCLI = [];

    /** @var array<string> */
    public array $packages = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->version = (string) ($data['version'] ?? '');
        $dto->config = $data['config'] ?? [];
        $dto->configWeb = $data['configWeb'] ?? [];
        $dto->configCLI = $data['configCLI'] ?? [];
        $dto->packages = $data['packages'] ?? [];
        
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'version' => $this->version,
            'config' => $this->config,
            'configWeb' => $this->configWeb,
            'configCLI' => $this->configCLI,
            'packages' => $this->packages,
        ];
    }
}