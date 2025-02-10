<?php

namespace App\Http\Requests;

use App\Enums\{MappingCategory, MappingSource, MappingType};
use Illuminate\Foundation\Http\FormRequest;

class MappingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'unique:mappings,code'],
            'path' => ['required', 'string', 'max:255'],
            'source' => ['required', 'enum:' . MappingSource::class],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'enum:' . MappingType::class],
            'default' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'enum:' . MappingCategory::class],
            'transformer' => ['nullable', 'string', 'max:255'],
            'options' => ['nullable', 'array'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
