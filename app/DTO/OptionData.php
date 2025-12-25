<?php

namespace App\DTO;

use App\Enums\SchemaOptionFieldType;
use Illuminate\Contracts\Support\Arrayable;

class OptionData implements Arrayable
{
    public function __construct(
        public string $name,
        public string $label,
        public SchemaOptionFieldType $type,
        public mixed $value,
        public mixed $defaultValue,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type->value,
            'value' => $this->value,
            'defaultValue' => $this->defaultValue,
        ];
    }
}