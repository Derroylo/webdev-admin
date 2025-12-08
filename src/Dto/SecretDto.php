<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class SecretDto
{
    public ?string $missingMessage = null;

    #[Assert\NotBlank]
    public ?string $sourceKey = null;

    public ?string $sourceGroup = null;

    public ?string $targetFile = null;

    public ?string $targetEnvVar = null;

    public array $targetExpectedSecrets = [];

    public array $targetExpectedVars = [];

    public static function fromArray(array $data): self
    {
        $dto                        = new self();
        $dto->missingMessage        = $data['missingMessage'] ?? null;
        $dto->sourceKey             = $data['source']['key'] ?? null;
        $dto->sourceGroup           = $data['source']['group'] ?? null;
        $dto->targetFile            = $data['target']['file'] ?? null;
        $dto->targetEnvVar          = $data['target']['envVar'] ?? null;
        $dto->targetExpectedSecrets = $data['target']['expectedSecrets'] ?? [];
        $dto->targetExpectedVars    = $data['target']['expectedVars'] ?? [];

        return $dto;
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->missingMessage !== null) {
            $result['missingMessage'] = $this->missingMessage;
        }

        $result['source'] = [
            'key' => $this->sourceKey,
        ];

        if ($this->sourceGroup !== null) {
            $result['source']['group'] = $this->sourceGroup;
        }

        $result['target'] = [];

        if ($this->targetFile !== null) {
            $result['target']['file'] = $this->targetFile;
        }

        if ($this->targetEnvVar !== null) {
            $result['target']['envVar'] = $this->targetEnvVar;
        }

        if (!empty($this->targetExpectedSecrets)) {
            $result['target']['expectedSecrets'] = $this->targetExpectedSecrets;
        }

        if (!empty($this->targetExpectedVars)) {
            $result['target']['expectedVars'] = $this->targetExpectedVars;
        }

        return $result;
    }
}
