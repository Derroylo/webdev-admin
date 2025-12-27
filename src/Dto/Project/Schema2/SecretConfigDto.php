<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class SecretConfigDto
{
    public string $missingMessage = '';

    public SecretSourceConfigDto $source;

    public SecretTargetConfigDto $target;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->missingMessage = $data['missingMessage'] ?? '';
        $dto->source = SecretSourceConfigDto::fromArray($data['source'] ?? []);
        $dto->target = SecretTargetConfigDto::fromArray($data['target'] ?? []);

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'missingMessage' => $this->missingMessage,
            'source' => $this->source->toArray(),
            'target' => $this->target->toArray(),
        ];
    }
}
