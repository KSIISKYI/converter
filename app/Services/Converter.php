<?php

namespace App\Services;

use App\Enums\InstanceStatus;
use App\Models\Instance;
use App\Repos\Instance\InstanceRepoInterface;
use App\Services\Instance\InstanceFileHelper;
use App\Services\Instance\InstanceFileManager;
use App\Services\Schemas\InstanceSettingsFactory;
use App\Services\Schemas\SchemaFactory;
use Illuminate\Log\LogManager;
use Throwable;

class Converter
{
    public function __construct(
        private InstanceRepoInterface $instanceRepo,
        private SchemaFactory $schemaFactory,
        private InstanceFileManager $fileManager,
        private InstanceSettingsFactory $instanceSettingsFactory,
        private InstanceFileHelper $fileHelper,
        private LogManager $logger,
    ) {
    }

    public function convert(Instance $instance): void
    {
        if ($instance->status !== InstanceStatus::PENDING) {
            return;
        }

        try {
            // Step 1: Update the status to processing
            $this->instanceRepo
                ->updateById(
                    $instance->id,
                    [
                        'status' => InstanceStatus::PROCESSING,
                    ]
                );

            // Step 2: Validate the instance
            $convertingStrategy = $this->schemaFactory->make($instance);
            $convertingStrategy->validate($instance);
            
            // Step 3: Convert the source content to the target content
            $targetContent = $convertingStrategy->convert(
                $this->fileManager->get($instance->original_file_path),
                $this->instanceSettingsFactory->makeReadingSettings($instance),
                $this->instanceSettingsFactory->makeConvertingSettings($instance),
            );
            
            // Step 4: Write the target content to the target file
            $convertedFilePath = $this->fileHelper->generateConvertedPath($instance);
            $this->fileManager->put($convertedFilePath, $targetContent);

            // Step 5: Update the instance status to completed and set the target path
            $this->instanceRepo
                ->updateById(
                    $instance->id,
                    [
                        'status' => InstanceStatus::COMPLETED,
                        'converted_file_path' => $convertedFilePath,
                    ]
                );
        } catch (Throwable $e) {
            $this->logger->error(
                'Error converting instance',
                [
                    'instance_id' => $instance->id,
                    'exception' => $e,
                ]
            );

            $this->instanceRepo
                ->updateById(
                    $instance->id,
                    [
                        'status' => InstanceStatus::FAILED,
                    ]
                );
        }
    }
}