<?php

namespace App\Services\Schemas\JsonToCsv;

use App\Enums\ConvertingSchemaType;
use App\Interfaces\ConvertingSettingsInterface;
use App\Interfaces\ReadingSettingsInterface;
use App\Models\Instance;
use App\Services\CSV\Converter;
use App\Services\JSON\Reader;
use App\Services\Schemas\ConvertingStrategyInterface;
use App\Services\Schemas\JsonToCsv\DTO\ConvertingSettings;
use App\Services\Schemas\JsonToCsv\DTO\ReadingSettings;

class ConvertingStrategy implements ConvertingStrategyInterface
{
    public function __construct(
        private readonly Reader $jsonReader,
        private readonly JsonSchemaExtractor $jsonSchemaExtractor,
        private readonly Converter $csvConverter,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function validate(Instance $instance): void
    {
        if ($instance->schema_type !== ConvertingSchemaType::JSON_TO_CSV) {
            throw new \Exception('Invalid schema type');
        }
    }

    /**
     * @param ReadingSettings $readingSettings
     * @param ConvertingSettings $convertingSettings
     *
     * {@inheritDoc}
     */
    public function convert(
        string $sourceContent,
        ReadingSettingsInterface $readingSettings,
        ConvertingSettingsInterface $convertingSettings,
    ): string
    {
        // Step 1. Read source content and convert to array
        $sourceContentArray = $this->jsonReader->toArray($sourceContent);

        // Step 2: Extract CSV schema and items based on JSON schema
        $csvSchema = $this->jsonSchemaExtractor->extractCsvSchema($readingSettings->schema);
        $items = $this->jsonSchemaExtractor->extractItems($sourceContentArray, $readingSettings->schema);

        // Step 3: Add items to CSV converter
        $this->csvConverter->setSchema($csvSchema);
        foreach ($items as $item) {
            $this->csvConverter->addItem($item);
        }

        // Step 4: Convert to CSV
        return $this->csvConverter->convert(
            $convertingSettings->separator,
            $convertingSettings->enclosure,
            $convertingSettings->escape,
            $convertingSettings->eol,
        );
    }
}