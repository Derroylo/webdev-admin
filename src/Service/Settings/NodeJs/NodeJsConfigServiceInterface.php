<?php

declare(strict_types=1);

namespace App\Service\Settings\NodeJs;

interface NodeJsConfigServiceInterface
{
    public function getNodeJsConfig(): array;

    public function updateNodeJsConfig(array $data): void;
}