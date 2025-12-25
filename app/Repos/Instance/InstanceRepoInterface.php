<?php

namespace App\Repos\Instance;

use App\Enums\ConvertingSchemaType;
use App\Models\Instance;
use App\Repos\RepoInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface InstanceRepoInterface extends RepoInterface
{
    /**
     * Creates a new instance with the given schema type
     * 
     * @param ConvertingSchemaType $schemaType
     * @return Instance
     */
    public function createBySchemaType(ConvertingSchemaType $schemaType): Instance;

    /**
     * Returns collection of instances by ids
     *
     * @param array $ids
     * @return Collection<Instance>
     */
    public function getByIds(array $ids): Collection;

    /**
     * Returns collection of instances that are created before the given date
     * 
     * @param Carbon $date
     * @return Collection<Instance>
     */
    public function getOutdatedInstances(Carbon $date): Collection;
}