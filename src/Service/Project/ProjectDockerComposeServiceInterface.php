<?php

declare(strict_types=1);

namespace App\Service\Project;

interface ProjectDockerComposeServiceInterface
{
    public function getDockerComposeFileContent(): array;

    public function saveDockerComposeFile(array $dockerComposeFileContent): void;

    public function getServices(): array;

    public function getService(string $serviceName): array;

    public function addService(string $serviceName, string $serviceDefinition): void;

    public function removeService(string $serviceName): void;   
}
