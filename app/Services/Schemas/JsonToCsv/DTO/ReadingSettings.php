<?php

namespace App\Services\Schemas\JsonToCsv\DTO;

use App\Interfaces\ReadingSettingsInterface;

readonly class ReadingSettings implements ReadingSettingsInterface
{
    public function __construct(
        public JsonSchemaData $schema,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $schemaJson = $data['schema'] ?? '{}';

        if (is_array($schemaJson)) {
            $schema = JsonSchemaData::fromArray($schemaJson);
        } else {
            $schema = JsonSchemaData::fromJson($schemaJson);
        }

        return new self(
            schema: $schema,
        );
    }

    public function toArray(): array
    {
        return [
            'schema' => $this->schema->toJson(),
        ];
    }
}