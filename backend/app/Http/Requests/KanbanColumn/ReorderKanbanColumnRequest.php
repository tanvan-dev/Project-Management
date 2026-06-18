<?php

namespace App\Http\Requests\KanbanColumn;

use Illuminate\Foundation\Http\FormRequest;

class ReorderKanbanColumnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'columns' => ['required', 'array', 'min:1'],
            'columns.*.id' => ['required', 'string', 'exists:kanban_columns,id'],
            'columns.*.position' => ['required', 'integer', 'min:0'],
        ];
    }
}
