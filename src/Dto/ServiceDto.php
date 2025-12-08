<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ServiceDto
{
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $category = null;

    public bool $active = false;

    #[Assert\Range(min: 1, max: 65535)]
    public ?int $port = null;

    public ?string $subDomain = null;

    public static function fromArray(array $data): self
    {
        $dto            = new self();
        $dto->name      = $data['name'] ?? null;
        $dto->category  = $data['category'] ?? null;
        $dto->active    = $data['active'] ?? false;
        $dto->port      = isset($data['port']) ? (int) $data['port'] : null;
        $dto->subDomain = $data['subDomain'] ?? null;

        return $dto;
    }

    public function toArray(): array
    {
        $result = [
            'name'     => $this->name,
            'category' => $this->category,
            'active'   => $this->active,
        ];

        if ($this->port !== null) {
            $result['port'] = $this->port;
        }

        if ($this->subDomain !== null) {
            $result['subDomain'] = $this->subDomain;
        }

        return $result;
    }
}
