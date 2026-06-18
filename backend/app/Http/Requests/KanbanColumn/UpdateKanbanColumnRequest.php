<?php

namespace App\Http\Requests\KanbanColumn;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKanbanColumnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'display_name' => ['sometimes', 'string', 'max:255'],
            'wip_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
