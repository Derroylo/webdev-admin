<?php

declare(strict_types=1);

namespace App\Dto;

class ProjectDto
{
    public string $name = '';
    public string $phpVersion = '';
    public string $nodejsVersion = '';
    public string $path = '';
    public bool $isWebdevCompatible = false;
    /** @var array<string, string> Array of test key => test name */
    public array $tests = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->name = $data['name'] ?? '';
        $dto->phpVersion = $data['phpVersion'] ?? '';
        $dto->nodejsVersion = $data['nodejsVersion'] ?? '';
        $dto->path = $data['path'] ?? '';
        $dto->isWebdevCompatible = $data['isWebdevCompatible'] ?? false;
        $dto->tests = $data['tests'] ?? [];
        
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phpVersion' => $this->phpVersion,
            'nodejsVersion' => $this->nodejsVersion,
            'path' => $this->path,
            'isWebdevCompatible' => $this->isWebdevCompatible,
            'tests' => $this->tests,
        ];
    }
}

