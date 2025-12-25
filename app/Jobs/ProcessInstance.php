<?php

namespace App\Jobs;

use App\Enums\InstanceStatus;
use App\Repos\Instance\InstanceRepoInterface;
use App\Services\Converter;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Log\LogManager;
use Throwable;

class ProcessInstance implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int $instanceId,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        InstanceRepoInterface $instanceRepo, 
        Converter $converter,
        LogManager $logger,
    ): void
    {
        $instance = $instanceRepo->getByIdOrNull($this->instanceId);

        if ($instance === null) {
            throw new \Exception('Instance not found');
        }

        try {
            $converter->convert($instance);
        } catch (Throwable $e) {
            $instanceRepo->updateById($instance->id, [
                'status' => InstanceStatus::FAILED,
            ]);
            $logger->error('Error converting instance', [
                'instance_id' => $instance->id,
                'exception' => $e,
            ]);

            throw $e;
        }
    }

    public function uniqueId(): string
    {
        return sprintf('queue.instances.processing.lock_for:%s', $this->instanceId);
    }
}
