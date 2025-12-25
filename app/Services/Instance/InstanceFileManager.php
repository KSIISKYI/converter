<?php

namespace App\Services\Instance;

use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InstanceFileManager
{
    public function __construct(
        private Filesystem $filesystem,
    ) {
    }

    public function store(string $content, string $path): void
    {
        if (!$this->filesystem->put($path, $content)) {
            throw new \Exception("Failed to write file: $path");
        }
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function get(string $path): string
    {
        $content = $this->filesystem->get($path);

        if ($content === null) {
            throw new \Exception("File not found: $path");
        }
        return $content;
    }
    
    public function put(string $path, string $content): void
    {
        if (!$this->filesystem->put($path, $content)) {
            throw new \Exception("Failed to write file: $path");
        }
    }

    public function cleanDirectory(string $path): void
    {
        if (!$this->filesystem->deleteDirectory($path)) {
            throw new \Exception("Failed to delete directory: $path");
        }
    }

    public function download(string $path, string $filename): StreamedResponse
    {
        if (!$this->exists($path)) {
            throw new \Exception("File not found: $path");
        }

        return $this->filesystem->download($path, $filename);
    }
}