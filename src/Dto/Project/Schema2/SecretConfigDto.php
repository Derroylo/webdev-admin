<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class SecretConfigDto
{
    public string $missingMessage = '';

    public string $sourceKey = '';

    public string $sourceGroup = '';

    public string $targetFile = '';

    public string $targetEnvVar = '';

    public array $targetExpectedSecrets = [];

    public array $targetExpectedVars = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->missingMessage = $data['missingMessage'] ?? '';
        $dto->sourceKey = $data['sourceKey'] ?? '';
        $dto->sourceGroup = $data['sourceGroup'] ?? '';
        $dto->targetFile = $data['targetFile'] ?? '';
        $dto->targetEnvVar = $data['targetEnvVar'] ?? '';
        $dto->targetExpectedSecrets = $data['targetExpectedSecrets'] ?? [];
        $dto->targetExpectedVars = $data['targetExpectedVars'] ?? [];

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'missingMessage' => $this->missingMessage,
            'sourceKey' => $this->sourceKey,
            'sourceGroup' => $this->sourceGroup,
            'targetFile' => $this->targetFile,
            'targetEnvVar' => $this->targetEnvVar,
            'targetExpectedSecrets' => $this->targetExpectedSecrets,
            'targetExpectedVars' => $this->targetExpectedVars,
        ];
    }
}
