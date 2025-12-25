<?php

namespace App\Interfaces;

use App\Interfaces\FromArrayInterface;
use Illuminate\Contracts\Support\Arrayable;

interface ConvertingSettingsInterface extends FromArrayInterface, Arrayable
{
}