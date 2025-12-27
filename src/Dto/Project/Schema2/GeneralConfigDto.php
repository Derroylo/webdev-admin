<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class GeneralConfigDto
{
    public string $workspaceFolder = '';

    public GeneralProxyConfigDto $proxy;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->workspaceFolder = $data['workspaceFolder'] ?? '';
        $dto->proxy = GeneralProxyConfigDto::fromArray($data['proxy'] ?? []);

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'workspaceFolder' => $this->workspaceFolder,
            'proxy' => $this->proxy->toArray(),
        ];
    }
}