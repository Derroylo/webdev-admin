<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PhpConfigDto
{
    #[Assert\NotBlank]
    public ?string $version = null;

    public array $config = [];

    public array $configWeb = [];

    public array $configCLI = [];

    public static function fromArray(array $data): self
    {
        $dto            = new self();
        $dto->version   = $data['version'] ?? null;
        $dto->config    = $data['config'] ?? [];
        $dto->configWeb = $data['configWeb'] ?? [];
        $dto->configCLI = $data['configCLI'] ?? [];

        return $dto;
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->version !== null) {
            $result['version'] = $this->version;
        }

        if (!empty($this->config)) {
            $result['config'] = $this->config;
        }

        if (!empty($this->configWeb)) {
            $result['configWeb'] = $this->configWeb;
        }

        if (!empty($this->configCLI)) {
            $result['configCLI'] = $this->configCLI;
        }

        return $result;
    }
}
