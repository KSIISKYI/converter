<?php

namespace App\Repos;

use Illuminate\Database\Eloquent\Model;

interface RepoInterface
{
    public function getByIdOrNull(int $id): ?Model;

    public function updateById(int $id, array $data): Model;

    public function delete($id, bool $force = false): bool|null;
}