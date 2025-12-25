<?php

namespace App\Http\Middleware;

use App\Services\Instance\SessionInstanceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstanceInSession
{
    public function __construct(
        private readonly SessionInstanceService $sessionInstanceService,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $instance = $request->route('instance');
        $instanceId = $instance instanceof \App\Models\Instance ? $instance->id : $instance;

        if (!$this->sessionInstanceService->exists($instanceId)) {
            return redirect()->route('instances.index');
        }

        return $next($request);
    }
}
