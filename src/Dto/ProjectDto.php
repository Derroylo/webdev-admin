<?php

declare(strict_types=1);

namespace App\Dto;

use App\Dto\Project\ProjectConfigInterfaceDto;

class ProjectDto
{
    public string $name             = '';

    public string $path             = '';

    public bool $isWebdevCompatible = false;

    public bool $isProjectRunning = false;

    public ?ProjectConfigInterfaceDto $config = null;

    public static function fromArray(array $data): self
    {
        $dto                     = new self();
        $dto->name               = $data['name'] ?? '';
        $dto->path               = $data['path'] ?? '';
        $dto->isWebdevCompatible = $data['isWebdevCompatible'] ?? false;
        $dto->isProjectRunning   = $data['isProjectRunning'] ?? false;
        $dto->config             = $data['config'] ?? null;

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'name'               => $this->name,
            'path'               => $this->path,
            'isWebdevCompatible' => $this->isWebdevCompatible,
            'isProjectRunning'   => $this->isProjectRunning,
            'config'             => $this->config?->toArray(),
        ];
    }
}
