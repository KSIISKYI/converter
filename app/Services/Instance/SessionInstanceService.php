<?php

namespace App\Services\Instance;

use Illuminate\Contracts\Session\Session;

class SessionInstanceService
{
    private const SESSION_INSTANCES_KEY = 'instances';
    
    public function __construct(
        private readonly Session $session
    ) {
    }

    /**
     * Returns the list of instance ids of the user stored in the session.
     * 
     * @return int[]
     */
    public function getInstances(): array
    {
        return $this->session->get(self::SESSION_INSTANCES_KEY, []);
    }

    public function exists(int $instanceId): bool
    {
        $instances = $this->getInstances();

        return in_array($instanceId, $instances);
    }

    public function addInstance(int $instanceId): void
    {
        $instances = $this->getInstances();

        $instances[] = $instanceId;

        $this->session->put(self::SESSION_INSTANCES_KEY, $instances);
    }

    public function removeInstance(int $instanceId): void
    {
        $instances = $this->getInstances();

        $instances = array_filter($instances, fn($id) => $id !== $instanceId);

        $this->session->put(self::SESSION_INSTANCES_KEY, $instances);
    }
}