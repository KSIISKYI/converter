<?php

namespace App\Services\Schemas\DTO;

use App\DTO\OptionData;
use Illuminate\Contracts\Support\Arrayable;

readonly class ConvertingSchemaData implements Arrayable
{
    /**
     * @param string $name
     * @param string $label
     * @param array<OptionData> $readingOptions
     * @param array<OptionData> $convertingOptions
     */
    public function __construct(
        public string $name,
        public string $label,
        public array $readingOptions,
        public array $convertingOptions,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'reading_options' => array_map(fn(OptionData $option) => $option->toArray(), $this->readingOptions),
            'converting_options' => array_map(fn(OptionData $option) => $option->toArray(), $this->convertingOptions),
        ];
    }
}