<?php

namespace App\Services\CSV\Extractor;

use Generator;

interface ExtractorStrategyInterface
{
    public function extractSchema(array $data): array;

    public function extractItems(array $data): Generator;
}