<?php

namespace App\Services\Schemas;

use App\DTO\OptionData;
use App\Enums\ConvertingSchemaType;
use App\Enums\SchemaOptionFieldType;

class SettingsValidator
{
    public function __construct(
        private OptionsProviderFactory $optionsProviderFactory
    ) {
    }

    /**
     * Validate reading settings array against schema options
     *
     * @param ConvertingSchemaType $schemaType
     * @param array $settings
     * @return array Array of validation errors (empty if valid)
     */
    public function validateReadingSettings(ConvertingSchemaType $schemaType, array $settings): array
    {
        $optionsProvider = $this->optionsProviderFactory->make($schemaType);
        $options = $optionsProvider->getReadingOptions();

        return $this->validateSettings($settings, $options);
    }

    /**
     * Validate converting settings array against schema options
     *
     * @param ConvertingSchemaType $schemaType
     * @param array $settings
     * @return array Array of validation errors (empty if valid)
     */
    public function validateConvertingSettings(ConvertingSchemaType $schemaType, array $settings): array
    {
        $optionsProvider = $this->optionsProviderFactory->make($schemaType);
        $options = $optionsProvider->getConvertingOptions();

        return $this->validateSettings($settings, $options);
    }

    /**
     * Validate settings array against options
     *
     * @param array $settings
     * @param OptionData[] $options
     * @param bool $requireAll If true, all options must be present. If false, only validate provided fields.
     * @return array Array of validation errors (empty if valid)
     */
    private function validateSettings(array $settings, array $options, bool $requireAll = false): array
    {
        // Allow empty arrays
        if (empty($settings)) {
            return [];
        }

        $errors = [];

        // Check for unknown fields first
        $optionNames = array_map(fn($option) => $option->name, $options);
        $extraFields = array_diff(array_keys($settings), $optionNames);

        if (!empty($extraFields)) {
            foreach ($extraFields as $field) {
                $errors[] = "Unknown field: {$field}";
            }
        }

        // Validate each option
        foreach ($options as $option) {
            if (!array_key_exists($option->name, $settings)) {
                if ($requireAll) {
                    $errors[] = "Missing required field: {$option->name}";
                }
                continue;
            }

            $value = $settings[$option->name];

            // Validate based on field type
            if ($option->type === SchemaOptionFieldType::DROPDOWN) {
                // Validate value against allowed values for dropdown
                if (is_array($option->value)) {
                    $allowedValues = array_values($option->value);

                    if (!in_array($value, $allowedValues, true)) {
                        $errors[] = "Invalid value for {$option->name}. Allowed values: " . implode(', ', $allowedValues);
                    }
                }
            } elseif ($option->type === SchemaOptionFieldType::JSON) {
                // Validate JSON syntax
                if (!is_string($value)) {
                    $errors[] = "Field {$option->name} must be a valid JSON string";
                    continue;
                }

                json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = "Field {$option->name} contains invalid JSON: " . json_last_error_msg();
                }
            }
        }

        return $errors;
    }

    /**
     * Check if reading settings are valid
     *
     * @param ConvertingSchemaType $schemaType
     * @param array $settings
     * @return bool
     */
    public function isValidReadingSettings(ConvertingSchemaType $schemaType, array $settings): bool
    {
        return empty($this->validateReadingSettings($schemaType, $settings));
    }

    /**
     * Check if converting settings are valid
     *
     * @param ConvertingSchemaType $schemaType
     * @param array $settings
     * @return bool
     */
    public function isValidConvertingSettings(ConvertingSchemaType $schemaType, array $settings): bool
    {
        return empty($this->validateConvertingSettings($schemaType, $settings));
    }
}
