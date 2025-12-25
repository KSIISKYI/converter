<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstanceCreateRequest;
use App\Http\Requests\InstanceUpdateRequest;
use App\Models\Instance;
use App\Services\Instance\InstanceService;
use App\Services\Schemas\Helpers\SchemasOptionsProvider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InstanceController extends Controller
{
    public function __construct(
        private readonly InstanceService $instanceService,
        private readonly SchemasOptionsProvider $schemasOptionsProvider,
    ) {
    }

    public function index(): View
    {
        $instances = $this->instanceService->getUserInstances();
        $schemas = $this->schemasOptionsProvider->getConvertingSchemas();

        return view(
            'instances.index',
            [
                'instances' => $instances,
                'schemas' => $schemas,
            ]
        );
    }

    public function show(Instance $instance): View
    {
        $schema = $this->schemasOptionsProvider->getConvertingSchema($instance->schema_type);

        return view(
            'instances.show',
            [
                'instance' => $instance,
                'schema' => $schema,
            ]
        );
    }

    public function store(InstanceCreateRequest $request): RedirectResponse
    {
        $instance = $this->instanceService->createInstance($request->getSchemaType());

        return redirect()->route('instances.show', $instance->id);
    }

    public function update(Instance $instance, InstanceUpdateRequest $request): RedirectResponse
    {
        if ($request->hasFile('file')) {
            $this->instanceService->uploadSourceFile($instance->id, $request->file('file'));
        }

        $this->instanceService->updateInstance(
            $instance->id,
            [
                'reading_settings' => $request->getReadingSettings(),
                'converting_settings' => $request->getConvertingSettings(),
            ]
        );

        return redirect()->route('instances.show', $instance->id);
    }

    public function destroy(Instance $instance): RedirectResponse
    {
        $this->instanceService->removeInstance($instance->id);

        return redirect()->route('instances.index');
    }

    public function convert(Instance $instance): RedirectResponse
    {
        $this->instanceService->runConvertingProcess($instance);

        return redirect()->route('instances.show', $instance->id);
    }

    public function download(Instance $instance): StreamedResponse
    {
        if (!$instance->status->isSuccessful() || !$instance->converted_file_path) {
            abort(404, 'Converted file not found');
        }

        return $this->instanceService->downloadConvertedFile($instance);
    }
}
