<?php

declare(strict_types=1);

namespace App\Dto\Command;

readonly class CommandExecutionRequestDto
{
    public function __construct(
        public string $projectPath,
        public string $command,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            projectPath: (string) ($data['projectPath'] ?? ''),
            command: (string) ($data['command'] ?? ''),
        );
    }
}
