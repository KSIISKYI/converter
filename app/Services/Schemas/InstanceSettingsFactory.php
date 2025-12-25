<?php

namespace App\Services\Schemas;

use App\Enums\ConvertingSchemaType;
use App\Interfaces\ConvertingSettingsInterface;
use App\Interfaces\ReadingSettingsInterface;
use App\Models\Instance;
use App\Services\Schemas\JsonToCsv\DTO\ReadingSettings as JsonToCsvReadingSettings;
use App\Services\Schemas\JsonToCsv\DTO\ConvertingSettings as JsonToCsvConvertingSettings;
class InstanceSettingsFactory
{
    public function makeReadingSettings(Instance $instance): ReadingSettingsInterface
    {
        return match ($instance->schema_type) {
            ConvertingSchemaType::JSON_TO_CSV => JsonToCsvReadingSettings::fromArray($instance->reading_settings),

            default => throw new \Exception('Invalid schema type'),
        };
    }

    public function makeConvertingSettings(Instance $instance): ConvertingSettingsInterface
    {
        return match ($instance->schema_type) {
            ConvertingSchemaType::JSON_TO_CSV => JsonToCsvConvertingSettings::fromArray($instance->converting_settings),

            default => throw new \Exception('Invalid schema type'),
        };
    }
}