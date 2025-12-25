@extends('layouts.app')

@section('title', 'Instance #' . $instance->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('instances.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-2 inline-block">
                ← Back to Instances
            </a>
        </div>
        <div class="flex space-x-2">
            @if(!$instance->status->isPending() && !$instance->status->isProcessing())
                <form action="{{ route('instances.destroy', $instance->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this instance?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors">
                        Delete Instance
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Instance Info Card -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Instance Information</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Schema Type</p>
                <p class="mt-1 text-sm text-gray-900">{{ $instance->schema_type->getLabel() }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status</p>
                <div class="mt-1">
                    @php
                        $statusColors = [
                            'created' => 'bg-gray-100 text-gray-800',
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                        ];
                        $statusColor = $statusColors[$instance->status->value] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                        {{ $instance->status->getLabel() }}
                    </span>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Created At</p>
                <p class="mt-1 text-sm text-gray-900">{{ $instance->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Updated At</p>
                <p class="mt-1 text-sm text-gray-900">{{ $instance->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if($instance->status->isCreated())
        <!-- Configuration Form -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Configure Instance</h3>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-md p-4">
                    <p class="font-medium">There were some errors with your submission:</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('instances.update', $instance->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- File Upload -->
                <div class="mb-6">
                    @if($instance->original_file_path)
                        <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm font-medium text-green-900 mb-1">Current File:</p>
                            <p class="text-sm text-green-700">{{ basename($instance->original_file_path) }}</p>
                        </div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            Replace Source File (Optional)
                            <span class="text-gray-500 font-normal">
                                (Supported: {{ implode(', ', $instance->schema_type->getSupportedSourceFileExtensions()) }}, Max: 20MB)
                            </span>
                        </label>
                        <input
                            type="file"
                            name="file"
                            id="file"
                            accept=".{{ implode(',.', $instance->schema_type->getSupportedSourceFileExtensions()) }}"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2"
                        >
                        <p class="mt-1 text-xs text-gray-500">Leave empty to keep the current file</p>
                    @else
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Source File *
                            <span class="text-gray-500 font-normal">
                                (Supported: {{ implode(', ', $instance->schema_type->getSupportedSourceFileExtensions()) }}, Max: 20MB)
                            </span>
                        </label>
                        <input
                            type="file"
                            name="file"
                            id="file"
                            required
                            accept=".{{ implode(',.', $instance->schema_type->getSupportedSourceFileExtensions()) }}"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2"
                        >
                    @endif
                </div>

                <!-- Reading Settings -->
                @if(!empty($schema->readingOptions))
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Reading Settings</h4>
                        <div class="space-y-4">
                            @foreach($schema->readingOptions as $option)
                                <div>
                                    <label for="reading_{{ $option->name }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $option->label }}
                                    </label>
                                    @if($option->type->value === 'dropdown')
                                        <select
                                            name="reading_settings[{{ $option->name }}]"
                                            id="reading_{{ $option->name }}"
                                            class="block w-full px-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-150 hover:border-gray-400"
                                        >
                                            @if(is_array($option->value))
                                                @foreach($option->value as $label => $value)
                                                    <option value="{{ $value }}" {{ ($instance->reading_settings[$option->name] ?? $option->defaultValue) == $value ? 'selected' : '' }}>
                                                        {{ $label }} ({{ $value }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @elseif($option->type->value === 'json')
                                        <div class="relative">
                                            <textarea
                                                name="reading_settings[{{ $option->name }}]"
                                                id="reading_{{ $option->name }}"
                                                rows="10"
                                                class="block w-full px-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-150 hover:border-gray-400 font-mono"
                                            >{{ $instance->reading_settings[$option->name] ?? $option->defaultValue }}</textarea>
                                            <div class="mt-2 flex items-center justify-between">
                                                <p class="text-xs text-gray-500">Enter valid JSON. Example: [{"label": "Name", "type": "string", "value": "$item.name"}]</p>
                                                <button
                                                    type="button"
                                                    onclick="validateAndFormatJSON('reading_{{ $option->name }}')"
                                                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-md transition-colors shadow-sm"
                                                >
                                                    Validate & Format
                                                </button>
                                            </div>
                                            <div id="reading_{{ $option->name }}_feedback" class="mt-2 hidden">
                                                <div class="feedback-message"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Converting Settings -->
                @if(!empty($schema->convertingOptions))
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Converting Settings</h4>
                        <div class="space-y-4">
                            @foreach($schema->convertingOptions as $option)
                                <div>
                                    <label for="converting_{{ $option->name }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $option->label }}
                                    </label>
                                    @if($option->type->value === 'dropdown')
                                        <select
                                            name="converting_settings[{{ $option->name }}]"
                                            id="converting_{{ $option->name }}"
                                            class="block w-full px-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-150 hover:border-gray-400"
                                        >
                                            @if(is_array($option->value))
                                                @foreach($option->value as $label => $value)
                                                    <option value="{{ $value }}" {{ ($instance->converting_settings[$option->name] ?? $option->defaultValue) == $value ? 'selected' : '' }}>
                                                        {{ $label }} ({{ $value }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @elseif($option->type->value === 'json')
                                        <div class="relative">
                                            <textarea
                                                name="converting_settings[{{ $option->name }}]"
                                                id="converting_{{ $option->name }}"
                                                rows="10"
                                                class="block w-full px-3 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-150 hover:border-gray-400 font-mono"
                                            >{{ $instance->converting_settings[$option->name] ?? $option->defaultValue }}</textarea>
                                            <div class="mt-2 flex items-center justify-between">
                                                <p class="text-xs text-gray-500">Enter valid JSON</p>
                                                <button
                                                    type="button"
                                                    onclick="validateAndFormatJSON('converting_{{ $option->name }}')"
                                                    class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-md transition-colors shadow-sm"
                                                >
                                                    Validate & Format
                                                </button>
                                            </div>
                                            <div id="converting_{{ $option->name }}_feedback" class="mt-2 hidden">
                                                <div class="feedback-message"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                    >
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>

        <!-- Convert Button -->
        @if($instance->original_file_path)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Start Conversion</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Your instance is configured and ready to convert. Click the button below to start the conversion process.
                </p>
                <form action="{{ route('instances.convert', $instance->id) }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors"
                    >
                        Start Conversion
                    </button>
                </form>
            </div>
        @endif
    @endif

    @if($instance->status->isPending() || $instance->status->isProcessing())
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Conversion in Progress</h3>
            <p class="text-sm text-blue-700">Your file is being converted. This may take a few moments...</p>
        </div>
    @endif

    @if($instance->status->isSuccessful())
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-green-900 mb-4">✓ Conversion Completed</h3>
            @if($instance->converted_file_path)
                <p class="text-sm text-green-700 mb-4">Your file has been successfully converted!</p>
                <div class="space-y-2">
                    <p class="text-xs text-green-600">
                        <strong>Converted File:</strong> {{ basename($instance->converted_file_path) }}
                    </p>
                    <a
                        href="{{ route('instances.download', $instance->id) }}"
                        class="inline-block px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors"
                    >
                        Download Converted File
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if($instance->status->isFailed())
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-red-900 mb-2">✗ Conversion Failed</h3>
            <p class="text-sm text-red-700">There was an error converting your file. Please try again or contact support.</p>
        </div>
    @endif
</div>

<script>
function validateAndFormatJSON(textareaId) {
    const textarea = document.getElementById(textareaId);
    const feedbackDiv = document.getElementById(textareaId + '_feedback');
    const feedbackMessage = feedbackDiv.querySelector('.feedback-message');

    if (!textarea || !feedbackDiv || !feedbackMessage) {
        return;
    }

    const value = textarea.value.trim();

    if (!value) {
        showFeedback(feedbackDiv, feedbackMessage, 'error', 'Please enter some JSON content');
        return;
    }

    try {
        // Try to parse the JSON
        const parsed = JSON.parse(value);

        // Format the JSON with proper indentation
        const formatted = JSON.stringify(parsed, null, 4);

        // Update textarea with formatted JSON
        textarea.value = formatted;

        // Show success message
        showFeedback(feedbackDiv, feedbackMessage, 'success', '✓ Valid JSON - Formatted successfully!');

        // Add green border to indicate valid JSON
        textarea.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
        textarea.classList.add('border-green-300', 'focus:ring-green-500', 'focus:border-green-500');

        // Hide feedback after 3 seconds
        setTimeout(() => {
            feedbackDiv.classList.add('hidden');
        }, 3000);

    } catch (error) {
        // Show error message
        showFeedback(feedbackDiv, feedbackMessage, 'error', '✗ Invalid JSON: ' + error.message);

        // Add red border to indicate invalid JSON
        textarea.classList.remove('border-green-300', 'focus:ring-green-500', 'focus:border-green-500');
        textarea.classList.add('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
    }
}

function showFeedback(feedbackDiv, feedbackMessage, type, message) {
    feedbackDiv.classList.remove('hidden');

    if (type === 'success') {
        feedbackMessage.className = 'feedback-message text-sm p-3 bg-green-50 border border-green-200 text-green-800 rounded-md';
    } else {
        feedbackMessage.className = 'feedback-message text-sm p-3 bg-red-50 border border-red-200 text-red-800 rounded-md';
    }

    feedbackMessage.textContent = message;
}

// Validate and format all JSON fields before form submission
function validateAllJSONFields() {
    const jsonTextareas = document.querySelectorAll('textarea.font-mono');
    let allValid = true;
    let firstInvalidField = null;

    // Hide server-side error block if it exists
    const serverErrorBlock = document.querySelector('.bg-red-50.border-red-200');
    if (serverErrorBlock && serverErrorBlock.textContent.includes('There were some errors')) {
        serverErrorBlock.style.display = 'none';
    }

    jsonTextareas.forEach(textarea => {
        const value = textarea.value.trim();

        if (value) {
            try {
                // Parse and validate JSON
                const parsed = JSON.parse(value);

                // Format the JSON
                const formatted = JSON.stringify(parsed, null, 4);
                textarea.value = formatted;

                // Mark as valid
                textarea.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                textarea.classList.add('border-green-300', 'focus:ring-green-500', 'focus:border-green-500');

                // Clear any error feedback
                const feedbackDiv = document.getElementById(textarea.id + '_feedback');
                if (feedbackDiv) {
                    feedbackDiv.classList.add('hidden');
                }
            } catch (error) {
                allValid = false;

                if (!firstInvalidField) {
                    firstInvalidField = textarea;
                }

                // Mark as invalid
                textarea.classList.remove('border-green-300', 'focus:ring-green-500', 'focus:border-green-500');
                textarea.classList.add('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');

                // Show error feedback
                const feedbackDiv = document.getElementById(textarea.id + '_feedback');
                const feedbackMessage = feedbackDiv?.querySelector('.feedback-message');

                if (feedbackDiv && feedbackMessage) {
                    showFeedback(feedbackDiv, feedbackMessage, 'error', '✗ Invalid JSON: ' + error.message);
                }
            }
        }
    });

    if (!allValid && firstInvalidField) {
        // Scroll to first invalid field
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstInvalidField.focus();
    }

    return allValid;
}

// Optional: Real-time validation on blur
document.addEventListener('DOMContentLoaded', function() {
    const jsonTextareas = document.querySelectorAll('textarea[id^="reading_"], textarea[id^="converting_"]');

    jsonTextareas.forEach(textarea => {
        // Only add validation for JSON fields
        if (textarea.classList.contains('font-mono')) {
            textarea.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value) {
                    try {
                        JSON.parse(value);
                        // Valid JSON - add green border
                        this.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                        this.classList.add('border-green-300');
                    } catch (error) {
                        // Invalid JSON - add red border
                        this.classList.remove('border-green-300', 'focus:ring-green-500', 'focus:border-green-500');
                        this.classList.add('border-red-300');
                    }
                }
            });
        }
    });

    // Add form submission validation for configuration form
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Check if this is the configuration update form
        const methodInput = form.querySelector('input[name="_method"][value="PUT"]');
        if (methodInput) {
            form.addEventListener('submit', function(e) {
                // Prevent default submission
                e.preventDefault();

                // Run validation and formatting
                const isValid = validateAllJSONFields();

                // If valid, submit the form
                if (isValid) {
                    // Remove event listener to avoid infinite loop
                    form.removeEventListener('submit', arguments.callee);
                    // Submit the form
                    form.submit();
                }

                return false;
            });
        }
    });
});
</script>
@endsection
