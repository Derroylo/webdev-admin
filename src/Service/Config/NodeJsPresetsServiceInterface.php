<?php

declare(strict_types=1);

namespace App\Service\Config;

interface NodeJsPresetsServiceInterface
{
    public function getNodeJsVersions(): array;
    public function getValidNodeJsVersions(): array;
    public function getNodeJsVersionRange(): array;
    public function getDefaultNodeJsVersion(): int;
}
