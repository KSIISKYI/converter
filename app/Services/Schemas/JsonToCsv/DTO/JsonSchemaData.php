<?php

namespace App\Services\Schemas\JsonToCsv\DTO;

readonly class JsonSchemaData
{
    /**
     * @param array<JsonSchemaColumnData> $columns
     */
    public function __construct(
        public array $columns = [],
    ) {
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        if ($data === null) {
            throw new \InvalidArgumentException('Invalid JSON schema');
        }

        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        $columns = [];

        foreach ($data as $columnData) {
            $columns[] = JsonSchemaColumnData::fromArray($columnData);
        }

        return new self(columns: $columns);
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->columns as $column) {
            $result[] = $column->toArray();
        }

        return $result;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getColumn(int $index): ?JsonSchemaColumnData
    {
        return $this->columns[$index] ?? null;
    }
}
