<?php

namespace App\Http\Requests;

use App\Enums\InstanceStatus;
use App\Repos\Instance\InstanceRepoInterface;
use App\Services\Schemas\SettingsValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class InstanceUpdateRequest extends FormRequest
{
    public function __construct(
        private readonly InstanceRepoInterface $instanceRepo,
        private readonly SettingsValidator $settingsValidator,
    ){
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $instance = $this->route('instance');
        $fileRule = $instance && $instance->original_file_path
            ? 'nullable|file|max:20480'
            : 'required|file|max:20480';

        return [
            'file' => $fileRule,
            'reading_settings' => 'array',
            'converting_settings' => 'array',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $instance = $this->route('instance');

            if (!$instance) {
                $validator->errors()->add('instance', 'Instance not found');
                return;
            }

            if ($instance->status !== InstanceStatus::CREATED) {
                $validator->errors()->add('instance', 'Instance is not in the correct status');
            }

            $file = $this->file('file');
            if ($file) {
                $fileExtension = strtolower($file->getClientOriginalExtension());

                if (!in_array($fileExtension, $instance->schema_type->getSupportedSourceFileExtensions())) {
                    $validator->errors()->add(
                        'file',
                        sprintf(
                            'Invalid file extension. Supported extensions: %s. Uploaded file has extension: %s',
                            implode(', ', $instance->schema_type->getSupportedSourceFileExtensions()),
                            $fileExtension
                        )
                    );
                }
            }

            // Validate reading settings
            $readingSettings = $this->input('reading_settings', []);
            $readingErrors = $this->settingsValidator->validateReadingSettings(
                $instance->schema_type,
                $readingSettings
            );

            foreach ($readingErrors as $error) {
                $validator->errors()->add('reading_settings', $error);
            }

            // Validate converting settings
            $convertingSettings = $this->input('converting_settings', []);
            $convertingErrors = $this->settingsValidator->validateConvertingSettings(
                $instance->schema_type,
                $convertingSettings
            );

            foreach ($convertingErrors as $error) {
                $validator->errors()->add('converting_settings', $error);
            }
        });
    }

    public function getReadingSettings(): array
    {
        return $this->input('reading_settings', []);
    }

    public function getConvertingSettings(): array
    {
        return $this->input('converting_settings', []);
    }

    public function messages()
    {
        return [
            'file.required' => 'The file field is required.',
            'file.file' => 'The file field must be a file.',
            'file.max' => 'Maximum file size is 20MB.',
        ];
    }
}