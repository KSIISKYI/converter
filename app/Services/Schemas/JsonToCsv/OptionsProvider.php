<?php

namespace App\Services\Schemas\JsonToCsv;

use App\DTO\OptionData;
use App\Enums\SchemaOptionFieldType;
use App\Services\CSV\Extractor\Enums\ExtractorStrategyType;
use App\Services\Schemas\OptionsProviderInterface;

class OptionsProvider implements OptionsProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getReadingOptions(): array
    {
        $defaultSchema = [
            [
                'label' => 'Column 1',
                'type' => 'string',
                'value' => '$item.0',
                'operations' => [],
            ],
        ];

        return [
            new OptionData(
                name: 'schema',
                label: 'Schema (JSON)',
                type: SchemaOptionFieldType::JSON,
                value: json_encode($defaultSchema, JSON_PRETTY_PRINT),
                defaultValue: json_encode($defaultSchema, JSON_PRETTY_PRINT),
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConvertingOptions(): array
    {
        return [
            new OptionData(
                name: 'separator',
                label: 'Separator',
                type: SchemaOptionFieldType::DROPDOWN,
                value: [
                    'Comma' => ',',
                ],
                defaultValue: ',',
            ),
            new OptionData(
                name: 'enclosure',
                label: 'Enclosure',
                type: SchemaOptionFieldType::DROPDOWN,
                value: [
                    'Double Quote' => '"',
                ],
                defaultValue: '"',
            ),
            new OptionData(
                name: 'escape',
                label: 'Escape',
                type: SchemaOptionFieldType::DROPDOWN,
                value: [
                    'Backslash' => '\\',
                ],
                defaultValue: '\\',
            ),
            new OptionData(
                name: 'eol',
                label: 'End of Line',
                type: SchemaOptionFieldType::DROPDOWN,
                value: [
                    'LF' => '\n',
                ],
                defaultValue: '\n',
            ),
        ];
    }
}