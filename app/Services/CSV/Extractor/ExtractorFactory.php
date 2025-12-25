<?php

namespace App\Services\CSV\Extractor;

use App\Services\CSV\Extractor\Enums\ExtractorStrategyType;
use App\Services\CSV\Extractor\Extractors\ArrayExtractor;

class ExtractorFactory
{
    public static function create(ExtractorStrategyType $strategy): ExtractorStrategyInterface
    {
        return match ($strategy) {
            ExtractorStrategyType::ARRAY => new ArrayExtractor(),
        };
    }
}