<?php

namespace App\Console\Commands;

use App\Services\Instance\InstanceService;
use Illuminate\Console\Command;

class RemoveOutdatedInstances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instances:remove-outdated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove outdated instances from the database and file system';

    /**
     * Execute the console command.
     */
    public function handle(InstanceService $instanceService) {
        // Calculate the date before which instances should be removed
        $date = now()->subMinutes(config('converting.lifetime'));

        $instanceService->removeOutdatedInstances($date);
    }
}
