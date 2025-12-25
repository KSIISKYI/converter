<?php

namespace App\Services\Instance;

use App\Models\Instance;
use Illuminate\Support\Str;

class InstanceFileHelper
{
    public function generateOriginalPath(Instance $instance, string $extension): string
    {
        return sprintf(
            'instances/%s/original/%s.%s',
            $instance->id,
            Str::uuid()->toString(),
            $extension,
        );
    }

    public function generateConvertedPath(Instance $instance): string
    {
        return sprintf(
            'instances/%s/converted/%s.%s',
            $instance->id,
            Str::uuid()->toString(),
            $instance->schema_type->getTargetFileExtension(),
        );
    }

    public function getInstanceFileDirectory(int $instanceId): string
    {
        return sprintf(
            'instances/%s',
            $instanceId,
        );
    }
}