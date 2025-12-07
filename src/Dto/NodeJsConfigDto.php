<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class NodeJsConfigDto
{
    #[Assert\NotBlank]
    public ?int $version = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->version = isset($data['version']) ? (int) $data['version'] : null;
        
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'version' => $this->version,
        ];
    }
}
