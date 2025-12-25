<?php

namespace App\Services\CSV;

class Converter
{
    private array $schema = [];
    private array $items = [];

    public function setSchema(array $schema): void
    {
        $this->schema = $schema;
    }

    public function addItem(array $item): void
    {
        $this->items[] = $item;
    }

    public function convert(
        string $separator,
        string $enclosure,
        string $escape,
        string $eol,
    ): string
    {
        $csv = [];

        // Convert escape sequences to actual characters
        $eol = $this->unescapeString($eol);
        $separator = $this->unescapeString($separator);
        $enclosure = $this->unescapeString($enclosure);
        $escape = $this->unescapeString($escape);

        if (!empty($this->schema)) {
            $csv[] = $this->formatCsvLine($this->schema, $separator, $enclosure, $escape);
        }

        foreach ($this->items as $item) {
            $row = [];
            foreach ($this->schema as $field) {
                $row[] = $item[$field] ?? '';
            }
            $csv[] = $this->formatCsvLine($row, $separator, $enclosure, $escape);
        }

        return implode($eol, $csv);
    }

    private function formatCsvLine(
        array $fields,
        string $separator,
        string $enclosure,
        string $escape,
    ): string
    {
        $formattedFields = [];

        foreach ($fields as $field) {
            $field = (string) $field;

            $needsEnclosure =
                str_contains($field, $separator) ||
                str_contains($field, $enclosure) ||
                str_contains($field, "\n") ||
                str_contains($field, "\r");

            if ($needsEnclosure || $field === '') {
                $field = str_replace(
                    $enclosure,
                    $escape . $enclosure,
                    $field
                );
                $formattedFields[] = $enclosure . $field . $enclosure;
            } else {
                $formattedFields[] = $field;
            }
        }

        return implode($separator, $formattedFields);
    }

    /**
     * Converts escape sequences in a string to their actual character representations.
     * Handles common escape sequences like \n, \r, \t, \\, etc.
     */
    private function unescapeString(string $string): string
    {
        $replacements = [
            '\\n' => "\n",   // newline
            '\\r' => "\r",   // carriage return
            '\\t' => "\t",   // tab
            '\\\\' => "\\",  // backslash
            '\\0' => "\0",   // null byte
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $string);
    }
}