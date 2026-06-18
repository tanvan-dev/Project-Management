<?php

namespace App\Http\Requests\KanbanColumn;

use Illuminate\Foundation\Http\FormRequest;

class StoreKanbanColumnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'],
            'wip_limit' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
