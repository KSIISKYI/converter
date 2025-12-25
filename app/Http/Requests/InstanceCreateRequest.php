<?php

namespace App\Http\Requests;

use App\Enums\ConvertingSchemaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstanceCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schema_type' => [
                'required',
                'string',
                Rule::in(array_column(ConvertingSchemaType::cases(), 'value')),
            ],
        ];
    }

    public function getSchemaType(): ConvertingSchemaType
    {
        return ConvertingSchemaType::from($this->input('schema_type'));
    }
}
