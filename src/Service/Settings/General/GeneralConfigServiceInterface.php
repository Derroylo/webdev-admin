<?php

declare(strict_types=1);

namespace App\Service\Settings\General;

interface GeneralConfigServiceInterface
{
    public function getGeneralConfig(): array;

    public function updateGeneralConfig(array $data): void;
}
