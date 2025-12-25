<?php

namespace App\Services\JSON;

use Exception;

class Reader
{
    public function toArray(string $json): array
    {
        $array = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Invalid JSON: %s', json_last_error_msg()));
        }

        return $array;
    }
}