<?php

namespace App\Services\Schemas;

use App\Enums\ConvertingSchemaType;
use App\Models\Instance;
use App\Services\Schemas\JsonToCsv\ConvertingStrategy as JsonToCsvConvertingStrategy;

class SchemaFactory
{
    public function make(Instance $instance): ConvertingStrategyInterface
    {
        $convertingStrategy = match ($instance->schema_type) {
            ConvertingSchemaType::JSON_TO_CSV => JsonToCsvConvertingStrategy::class,

            default => throw new \Exception('Invalid schema type'),
        };

        return app($convertingStrategy);
    }
}