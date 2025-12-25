<?php

namespace App\Services\Schemas;

use App\Interfaces\ConvertingSettingsInterface;
use App\Interfaces\ReadingSettingsInterface;
use App\Models\Instance;

interface ConvertingStrategyInterface
{
    public function validate(Instance $instance): void;

    public function convert(
        string $sourceContent,
        ReadingSettingsInterface $readingSettings,
        ConvertingSettingsInterface $convertingSettings,
    ): string;
}