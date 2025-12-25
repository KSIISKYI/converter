<?php

namespace App\Services\Schemas;

use App\DTO\OptionData;

interface OptionsProviderInterface
{
    /**
     * Get the reading options for the schema
     * 
     * @return OptionData[]
     */
    public function getReadingOptions(): array;

    /**
     * Get the converting options for the schema
     * 
     * @return OptionData[]
     */
    public function getConvertingOptions(): array;
}