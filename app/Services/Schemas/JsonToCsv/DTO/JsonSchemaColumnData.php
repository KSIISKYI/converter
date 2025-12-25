<?php

namespace App\Services\Schemas\JsonToCsv\DTO;

readonly class JsonSchemaColumnData
{
    /**
     * @param string $label
     * @param string $type
     * @param string $value
     * @param array<OperationData> $operations
     */
    public function __construct(
        public string $label,
        public string $type,
        public string $value,
        public array $operations = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $operations = [];
        if (isset($data['operations']) && is_array($data['operations'])) {
            foreach ($data['operations'] as $operationData) {
                $operations[] = OperationData::fromArray($operationData);
            }
        }

        return new self(
            label: $data['label'] ?? '',
            type: $data['type'] ?? 'string',
            value: $data['value'] ?? '',
            operations: $operations,
        );
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'type' => $this->type,
            'value' => $this->value,
            'operations' => array_map(fn($op) => $op->toArray(), $this->operations),
        ];
    }
}
