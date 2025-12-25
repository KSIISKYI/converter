<?php

namespace App\Services\Schemas\JsonToCsv;

use App\Services\Schemas\JsonToCsv\DTO\JsonSchemaData;
use App\Services\Schemas\JsonToCsv\DTO\OperationData;

class JsonSchemaExtractor
{
    /**
     * List of allowed operations
     */
    private const ALLOWED_OPERATIONS = [
        'split',
    ];

    /**
     * Required properties for each operation
     */
    private const OPERATION_REQUIRED_PROPERTIES = [
        'split' => [
            'delimiter',
            'index',
        ],
    ];
    /**
     * Extracts the CSV schema (column headers) from the JSON schema.
     *
     * @param JsonSchemaData $schema
     * @return array
     */
    public function extractCsvSchema(JsonSchemaData $schema): array
    {
        $csvSchema = [];

        foreach ($schema->columns as $column) {
            $csvSchema[] = $column->label;
        }

        return $csvSchema;
    }

    /**
     * Extracts items from the JSON data based on the schema.
     *
     * @param array $jsonData
     * @param JsonSchemaData $schema
     * @return array
     */
    public function extractItems(array $jsonData, JsonSchemaData $schema): array
    {
        $items = [];

        foreach ($jsonData as $item) {
            $extractedItem = $this->extractItem($item, $schema);
            $items[] = $extractedItem;
        }

        return $items;
    }

    /**
     * Extracts a single item from JSON data based on the schema.
     *
     * @param mixed $item
     * @param JsonSchemaData $schema
     * @return array
     */
    private function extractItem($item, JsonSchemaData $schema): array
    {
        $extractedItem = [];

        foreach ($schema->columns as $column) {
            // Step 1: Extract value using path
            $value = $this->extractValue($item, $column->value);

            // Step 2: Apply operations if any
            if (!empty($column->operations)) {
                $value = $this->applyOperations($value, $column->operations);
            }

            // Step 3: Convert type
            $convertedValue = $this->convertType($value, $column->type);
            $extractedItem[$column->label] = $convertedValue;
        }

        return $extractedItem;
    }

    /**
     * Extracts a value from an item using a path expression like "$item.name" or "$item.0".
     *
     * @param mixed $item
     * @param string $path
     * @return mixed
     */
    private function extractValue($item, string $path)
    {
        // Remove "$item." prefix if present
        $path = preg_replace('/^\$item\./', '', $path);

        // Handle empty path
        if ($path === '' || $path === '$item') {
            return $item;
        }

        return data_get($item, $path);
    }

    /**
     * Converts a value to the specified type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private function convertType($value, string $type)
    {
        if ($value === null) {
            return '';
        }

        return match ($type) {
            'integer', 'int' => (int)$value,
            'float', 'double' => (float)$value,
            'boolean', 'bool' => (bool)$value,
            'string' => (string)$value,

            default => $value,
        };
    }

    /**
     * Apply operations sequentially to a value.
     *
     * @param mixed $value
     * @param array<OperationData> $operations
     * @return mixed
     * @throws \Exception
     */
    private function applyOperations($value, array $operations)
    {
        foreach ($operations as $operation) {
            $value = $this->executeOperation($value, $operation);
        }

        return $value;
    }

    /**
     * Execute a single operation on a value.
     *
     * @param mixed $value
     * @param OperationData $operation
     * @return mixed
     * @throws \Exception
     */
    private function executeOperation($value, OperationData $operation)
    {
        // Validate operation type is allowed
        if (!in_array($operation->type, self::ALLOWED_OPERATIONS, true)) {
            throw new \Exception("Operation '{$operation->type}' is not allowed. Allowed operations: " . implode(', ', self::ALLOWED_OPERATIONS));
        }

        // Validate required properties
        $this->validateOperationProperties($operation);

        // Execute operation
        return match ($operation->type) {
            'split' => $this->operationSplit($value, $operation),

            default => $value,
        };
    }

    /**
     * Validate that required properties are present.
     *
     * @param OperationData $operation
     * @return void
     * @throws \Exception
     */
    private function validateOperationProperties(OperationData $operation): void
    {
        $requiredProperties = self::OPERATION_REQUIRED_PROPERTIES[$operation->type] ?? [];

        foreach ($requiredProperties as $property) {
            if (!isset($operation->properties[$property])) {
                throw new \Exception("Operation '{$operation->type}' requires property '{$property}'");
            }
        }
    }

    /**
     * Split operation: splits a string by delimiter and returns element at index.
     *
     * @param mixed $value
     * @param OperationData $operation
     * @return mixed
     */
    private function operationSplit($value, OperationData $operation)
    {
        if ($value === null) {
            return null;
        }

        $delimiter = $operation->getProperty('delimiter');
        $index = (int)$operation->getProperty('index');

        $parts = explode((string)$delimiter, (string)$value);

        return isset($parts[$index]) ? trim($parts[$index]) : null;
    }
}
