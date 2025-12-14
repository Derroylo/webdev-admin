<?php

declare(strict_types=1);

namespace App\Dto;

readonly class TestExecutionRequestDto
{
    public function __construct(
        public string $projectPath,
        public string $testKey,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            projectPath: (string) ($data['projectPath'] ?? ''),
            testKey: (string) ($data['testKey'] ?? ''),
        );
    }
}
