<?php

namespace App\Repos\Instance;

use App\Enums\ConvertingSchemaType;
use App\Enums\InstanceStatus;
use App\Models\Instance;
use App\Repos\AbstractEloquentRepo;
use App\Repos\Instance\InstanceRepoInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EloquentInstance extends AbstractEloquentRepo implements InstanceRepoInterface
{
    /**
     * @inheritDoc
     */
    public function getByIds(array $ids): Collection
    {
        return $this->model
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function createBySchemaType(ConvertingSchemaType $schemaType): Instance
    {
        return $this->model->create([
            'schema_type' => $schemaType,
            'status' => InstanceStatus::CREATED,
        ]);
    }

    /** 
     * @inheritDoc
     */
    public function getOutdatedInstances(Carbon $date): Collection
    {
        return $this->model
            ->where('updated_at', '<', $date)
            ->get();
    }
}