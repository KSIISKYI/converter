<?php

namespace App\Interfaces;

interface FromArrayInterface
{
    public static function fromArray(array $data): self;
}