<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TestDto
{
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Count(min: 1)]
    public array $commands = [];

    public array $tests = [];

    public static function fromArray(array $data): self
    {
        $dto           = new self();
        $dto->name     = $data['name'] ?? null;
        $dto->commands = $data['commands'] ?? [];
        $dto->tests    = $data['tests'] ?? [];

        return $dto;
    }

    public function toArray(): array
    {
        $result = [
            'name'     => $this->name,
            'commands' => array_values(array_filter($this->commands)),
        ];

        if (!empty($this->tests)) {
            $result['tests'] = array_values(array_filter($this->tests));
        }

        return $result;
    }
}
