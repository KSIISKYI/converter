<?php

namespace App\Services\Schemas\JsonToCsv\DTO;

use App\Interfaces\ConvertingSettingsInterface;

readonly class ConvertingSettings implements ConvertingSettingsInterface
{

    public function __construct(
        public string $separator,
        public string $enclosure,
        public string $escape,
        public string $eol,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            separator: $data['separator'],
            enclosure: $data['enclosure'],
            escape: $data['escape'],
            eol: $data['eol'],
        );
    }

    public function toArray(): array
    {
        return [
            'separator' => $this->separator,
            'enclosure' => $this->enclosure,
            'escape' => $this->escape,
            'eol' => $this->eol,
        ];
    }
}