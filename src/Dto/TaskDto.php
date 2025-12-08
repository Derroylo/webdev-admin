<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TaskDto
{
    #[Assert\NotBlank]
    public ?string $name = null;

    public bool $onlyMain = false;

    public array $init = [];

    public array $create = [];

    public array $start = [];

    public static function fromArray(array $data): self
    {
        $dto           = new self();
        $dto->name     = $data['name'] ?? null;
        $dto->onlyMain = $data['onlyMain'] ?? false;
        $dto->init     = $data['init'] ?? [];
        $dto->create   = $data['create'] ?? [];
        $dto->start    = $data['start'] ?? [];

        return $dto;
    }

    public function toArray(): array
    {
        $result = [
            'name' => $this->name,
        ];

        if ($this->onlyMain) {
            $result['onlyMain'] = $this->onlyMain;
        }

        if (!empty($this->init)) {
            $result['init'] = array_values(array_filter($this->init));
        }

        if (!empty($this->create)) {
            $result['create'] = array_values(array_filter($this->create));
        }

        if (!empty($this->start)) {
            $result['start'] = array_values(array_filter($this->start));
        }

        return $result;
    }
}
