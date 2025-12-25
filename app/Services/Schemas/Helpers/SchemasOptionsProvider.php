<?php

namespace App\Services\Schemas\Helpers;

use App\Enums\ConvertingSchemaType;
use App\Services\Schemas\DTO\ConvertingSchemaData;
use App\Services\Schemas\OptionsProviderFactory;

class SchemasOptionsProvider
{
    public function __construct(
        private readonly OptionsProviderFactory $optionsProviderFactory,
    ) {
    }

    /**
     * Gets a specific converting schema by type.
     *
     * @param ConvertingSchemaType $type
     * @return ConvertingSchemaData
     */
    public function getConvertingSchema(ConvertingSchemaType $type): ConvertingSchemaData
    {
        $optionsProvider = $this->optionsProviderFactory->make($type);

        return new ConvertingSchemaData(
            name: $type->value,
            label: $type->getLabel(),
            readingOptions: $optionsProvider->getReadingOptions(),
            convertingOptions: $optionsProvider->getConvertingOptions(),
        );
    }

    /**
     * Gets the converting schemas with their reading and converting options.
     *
     * @return ConvertingSchemaData[]
     */
    public function getConvertingSchemas(): array
    {
        $schemas = [];

        foreach (ConvertingSchemaType::cases() as $schemaType) {
            $schemas[] = $this->getConvertingSchema($schemaType);
        }

        return $schemas;
    }
}