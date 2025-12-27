<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class GeneralProxyConfigDto
{
    public string $domain = '';
    public string $subDomain = '';

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->domain = $data['domain'] ?? '';
        $dto->subDomain = $data['subDomain'] ?? '';
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'domain' => $this->domain,
            'subDomain' => $this->subDomain,
        ];
    }
}