<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class WorkspaceDto
{
    public ?string $name = null;

    public ?string $description = null;

    #[Assert\Url]
    public ?string $repository = null;

    public ?string $branch = null;

    public ?string $folder = null;

    public ?string $docRoot = 'public';

    #[Assert\Choice(choices: ['vhost'])]
    public ?string $mode = 'vhost';

    public array $subDomains = [];

    public bool $disableWeb = false;

    public static function fromArray(array $data): self
    {
        $dto              = new self();
        $dto->name        = $data['name'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->repository  = $data['repository'] ?? null;
        $dto->branch      = $data['branch'] ?? null;
        $dto->folder      = $data['folder'] ?? null;
        $dto->docRoot     = $data['docRoot'] ?? 'public';
        $dto->mode        = $data['mode'] ?? 'vhost';
        $dto->subDomains  = $data['subDomains'] ?? [];
        $dto->disableWeb  = $data['disableWeb'] ?? false;

        return $dto;
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->name !== null) {
            $result['name'] = $this->name;
        }

        if ($this->description !== null) {
            $result['description'] = $this->description;
        }

        if ($this->repository !== null) {
            $result['repository'] = $this->repository;
        }

        if ($this->branch !== null) {
            $result['branch'] = $this->branch;
        }

        if ($this->folder !== null) {
            $result['folder'] = $this->folder;
        }

        if ($this->docRoot !== null && $this->docRoot !== 'public') {
            $result['docRoot'] = $this->docRoot;
        }

        if ($this->mode !== null && $this->mode !== 'vhost') {
            $result['mode'] = $this->mode;
        }

        if (!empty($this->subDomains)) {
            $result['subDomains'] = $this->subDomains;
        }

        if ($this->disableWeb) {
            $result['disableWeb'] = $this->disableWeb;
        }

        return $result;
    }
}
