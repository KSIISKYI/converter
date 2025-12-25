<?php

namespace App\Services\Schemas\JsonToCsv\DTO;

class OperationData
{
    public function __construct(
        public string $type,
        public array $properties = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] ?? '',
            properties: $data['properties'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'properties' => $this->properties,
        ];
    }

    public function getProperty(string $key, $default = null)
    {
        return $this->properties[$key] ?? $default;
    }
}
