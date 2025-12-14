<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class TestConfigDto
{
    public string $name = '';

    /** @var array<string> */
    public array $commands = [];

    /** @var array<string> */
    public array $tests = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->name = $data['name'] ?? '';
        $dto->commands = $data['commands'] ?? [];
        $dto->tests = $data['tests'] ?? [];
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'commands' => $this->commands,
            'tests' => $this->tests,
        ];
    }
}