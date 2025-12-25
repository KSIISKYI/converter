<?php

namespace App\Services\CSV\Extractor\Extractors;

use App\Services\CSV\Extractor\ExtractorStrategyInterface;
use Exception;
use Generator;

class ArrayExtractor implements ExtractorStrategyInterface
{
    public function extractSchema(array $data): array
    {
        $schema = $data['schema'] ?? [];
        $this->validateItem($schema);

        return $schema;
    }

    public function extractItems(array $data): Generator
    {
        foreach ($data['items'] ?? [] as $item) {
            $this->validateItem($item);

            yield $item;
        }
    }

    public function validateItem(mixed $item): void
    {
        if (!is_array($item)) {
            throw new Exception('Item is not an array');
        }

        foreach ($item as $value) {
            if (is_array($value)) {
                throw new Exception('Only one level of nesting is allowed');
            }
        }
    }
}