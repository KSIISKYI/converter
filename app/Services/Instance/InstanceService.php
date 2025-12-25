<?php

namespace App\Services\Instance;

use App\Enums\ConvertingSchemaType;
use App\Enums\InstanceStatus;
use App\Jobs\ProcessInstance;
use App\Models\Instance;
use App\Repos\Instance\InstanceRepoInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Log\LogManager;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class InstanceService
{
    public function __construct(
        private readonly InstanceRepoInterface $instanceRepo,
        private readonly InstanceFileHelper $instanceFileHelper,
        private readonly InstanceFileManager $instanceFileManager,
        private readonly SessionInstanceService $sessionInstanceService,
        private readonly LogManager $logger,
    ) {
    }

    /**
     * Returns the list of instances that are associated with the user.
     * Currently, the list of instances is stored in the session.
     * 
     * @return Collection<Instance>
     */
    public function getUserInstances(): Collection
    {
        $instanceIds = $this->sessionInstanceService->getInstances();

        return $this->instanceRepo->getByIds($instanceIds);
    }

    public function createInstance(ConvertingSchemaType $schemaType): Instance
    {
        $instance = $this->instanceRepo->createBySchemaType($schemaType);
        $this->sessionInstanceService->addInstance($instance->id);

        return $instance;
    }

    /**
     * @param Instance $instance
     * @param array $data
     * @return void
     */
    public function updateInstance(int $instanceId, array $data): void
    {
        $instance = $this->instanceRepo->getByIdOrNull($instanceId);

        if ($instance === null) {
            throw new Exception('Instance not found');
        }

        if ($instance->status !== InstanceStatus::CREATED) {
            throw new Exception('Instance is not in the correct status');
        }

        $this->instanceRepo->updateById($instanceId, $data);
    }

    /**
     * Uploads the source file for the given instance.
     * - Generates the original path for the source file
     * - Stores the source file in the file system
     * - Updates the instance with the original path
     * 
     * @param int $instanceId
     * @param UploadedFile $file
     * @throws Exception
     * @return void
     */
    public function uploadSourceFile(int $instanceId, UploadedFile $file): void
    {
        $instance = $this->instanceRepo->getByIdOrNull($instanceId);

        if ($instance === null) {
            throw new Exception('Instance not found');
        }

        $originalPath = $this->instanceFileHelper->generateOriginalPath($instance, $file->getClientOriginalExtension());
        $this->instanceFileManager->store($file->get(), $originalPath);

        $this->instanceRepo->updateById($instanceId, [
            'original_file_path' => $originalPath,
        ]);
    }

    /**
     * Runs the converting process for the given instance.
     * - Updates the instance status to pending
     * - Dispatches the job to process the instance
     * 
     * @param Instance $instance
     * @return void
     */
    public function runConvertingProcess(Instance $instance): void
    {
        if ($instance->status !== InstanceStatus::CREATED) {
            throw new Exception('Instance is not in the correct status');
        }

        $this->instanceRepo->updateById($instance->id, [
            'status' => InstanceStatus::PENDING,
        ]);

        dispatch(new ProcessInstance($instance->id));
    }

    /**
     * It removes the instance from the system.
     *  - Removes the instance from the session
     *  - Cleans the instance files from the file system
     *  - Deletes the instance from the database
     * 
     * @param int $instanceId
     * @throws Exception
     * @return void
     */
    public function removeInstance(int $instanceId): void
    {
        $instance = $this->instanceRepo->getByIdOrNull($instanceId);

        if ($instance === null) {
            throw new Exception('Instance not found');
        }

        $this->instanceFileManager->cleanDirectory($this->instanceFileHelper->getInstanceFileDirectory($instanceId));
        $this->instanceRepo->delete($instanceId);
    }


    /**
     * It removes all instances from the system that are older than the given date.
     *
     * @param Carbon $date
     * @return void
     */
    public function removeOutdatedInstances(Carbon $date): void
    {
        $outdatedInstances = $this->instanceRepo->getOutdatedInstances($date);

        foreach ($outdatedInstances as $instance) {
            try {
                $this->removeInstance($instance->id);
            } catch (Throwable $e) {
                $this->logger->error('Error removing outdated instance', [
                    'instance_id' => $instance->id,
                    'exception' => $e,
                ]);
            }
        }
    }

    /**
     * Downloads the converted file for the given instance.
     *
     * @param Instance $instance
     * @return StreamedResponse
     * @throws Exception
     */
    public function downloadConvertedFile(Instance $instance): StreamedResponse
    {
        if (!$instance->converted_file_path) {
            throw new Exception('Converted file not found');
        }

        $filename = basename($instance->converted_file_path);

        return $this->instanceFileManager->download($instance->converted_file_path, $filename);
    }
}