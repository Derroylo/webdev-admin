<?php

declare(strict_types=1);

namespace App\Dto\Project;

abstract class AbstractProjectConfigDto
{
    abstract public function toArray(): array;

    abstract public static function fromArray(array $data): self;
}