<?php

namespace App\Enums;

enum ConvertingSchemaType: string
{
    case JSON_TO_CSV = 'json_to_csv';

    public function getSupportedSourceFileExtensions(): array
    {
        return match ($this) {
            self::JSON_TO_CSV => ['json'],

            default => [],
        };
    }

    public function getTargetFileExtension(): string
    {
        return match ($this) {
            self::JSON_TO_CSV => 'csv',

            default => throw new \Exception('Invalid schema type: ' . $this->value),
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::JSON_TO_CSV => 'JSON to CSV',

            default => throw new \Exception('Invalid schema type: ' . $this->value),
        };
    }
}