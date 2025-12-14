<?php

declare(strict_types=1);

namespace App\Dto\Project\Schema2;

class WorkspaceConfigDto
{
    public string $name = '';
    
    public string $description = '';

    public string $repository = '';

    public string $branch = '';

    public string $folder = '';

    public string $docRoot = 'public';

    public string $mode = 'vhost';

    public array $subDomains = [];

    public bool $disableWeb = false;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->name = $data['name'] ?? '';
        $dto->description = $data['description'] ?? '';
        $dto->repository = $data['repository'] ?? '';
        $dto->branch = $data['branch'] ?? '';
        $dto->folder = $data['folder'] ?? '';
        $dto->docRoot = $data['docRoot'] ?? 'public';
        $dto->mode = $data['mode'] ?? 'vhost';
        $dto->subDomains = $data['subDomains'] ?? [];
        $dto->disableWeb = $data['disableWeb'] ?? false;
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'repository' => $this->repository,
            'branch' => $this->branch,
            'folder' => $this->folder,
            'docRoot' => $this->docRoot,
            'mode' => $this->mode,
            'subDomains' => $this->subDomains,
            'disableWeb' => $this->disableWeb,
        ];
    }
}