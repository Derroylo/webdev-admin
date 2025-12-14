<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

use App\Dto\Project\ProjectConfigInterfaceDto;

class ProjectConfigDto implements ProjectConfigInterfaceDto
{
    public int $schemaVersion = 2;
    
    public GeneralConfigDto $config;

    public PhpConfigDto $php;

    public NodeJsConfigDto $nodejs;

    public ServicesConfigDto $services;

    /** @var array<SecretConfigDto> */
    public array $secrets = [];

    /** @var array<TaskConfigDto> */
    public array $tasks = [];

    /** @var array<TestConfigDto> */
    public array $tests = [];

    /** @var array<WorkspaceConfigDto> */
    public array $workspaces = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->config = GeneralConfigDto::fromArray($data['config'] ?? []);
        $dto->php = PhpConfigDto::fromArray($data['php'] ?? []);
        $dto->nodejs = NodeJsConfigDto::fromArray($data['nodejs'] ?? []);
        $dto->services = ServicesConfigDto::fromArray($data['services'] ?? []);
        $dto->secrets = array_map(fn(array $secret) => SecretConfigDto::fromArray($secret), $data['secrets'] ?? []);
        $dto->tasks = array_map(fn(array $task) => TaskConfigDto::fromArray($task), $data['tasks'] ?? []);
        $dto->tests = array_map(fn(array $test) => TestConfigDto::fromArray($test), $data['tests'] ?? []);
        $dto->workspaces = array_map(fn(array $workspace) => WorkspaceConfigDto::fromArray($workspace), $data['workspaces'] ?? []);

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'config' => $this->config->toArray(),
            'php' => $this->php->toArray(),
            'nodejs' => $this->nodejs->toArray(),
            'services' => $this->services->toArray(),
            'secrets' => array_map(fn(SecretConfigDto $secret) => $secret->toArray(), $this->secrets),
            'tasks' => array_map(fn(TaskConfigDto $task) => $task->toArray(), $this->tasks),
            'tests' => array_map(fn(TestConfigDto $test) => $test->toArray(), $this->tests),
            'workspaces' => array_map(fn(WorkspaceConfigDto $workspace) => $workspace->toArray(), $this->workspaces),
        ];
    }
}
