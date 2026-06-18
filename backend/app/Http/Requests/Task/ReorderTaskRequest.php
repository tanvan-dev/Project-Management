<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'in:todo,in_progress,in_review,done'],
            'position' => ['required', 'integer', 'min:0'],
            'sprint_id' => ['nullable', 'string', 'exists:sprints,id'],
        ];
    }
}
