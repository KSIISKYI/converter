<?php

namespace App\Repos;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentRepo implements RepoInterface
{
    public function __construct(
        protected Model $model,
    ) {
    }

    public function getByIdOrNull(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function updateById(int $id, array $data): Model
    {
        $model = $this->model->findOrFail($id);
        $model->update($data);

        return $model->fresh();
    }

    public function delete($id, bool $force = false): bool|null
    {
        $query = $this->model->where($this->model->getKeyName(), '=', $id);
        if ($force) {
            return $query->forceDelete();
        }

        return $query->delete();
    }
}