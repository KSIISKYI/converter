<?php

namespace App\Services\Schemas;

use App\Enums\ConvertingSchemaType;
use App\Services\Schemas\JsonToCsv\OptionsProvider as JsonToCsvOptionsProvider;

class OptionsProviderFactory
{
    public function make(ConvertingSchemaType $schemaType): OptionsProviderInterface
    {
        return match ($schemaType) {
            ConvertingSchemaType::JSON_TO_CSV => new JsonToCsvOptionsProvider(),

            default => throw new \Exception('Invalid schema type: ' . $schemaType->value),
        };
    }
}
