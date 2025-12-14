<?php

declare(strict_types=1);

namespace App\Dto\Project;

interface ProjectConfigInterfaceDto
{
    public function toArray(): array;

    public static function fromArray(array $data): self;
}