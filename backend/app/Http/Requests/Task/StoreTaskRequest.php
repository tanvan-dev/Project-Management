<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:todo,in_progress,in_review,done'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
            'due_date' => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'sprint_id' => ['nullable', 'string', 'exists:sprints,id'],
            'parent_task_id' => ['nullable', 'string', 'exists:tasks,id'],
            'position' => ['sometimes', 'integer', 'min:0'],
            'assignee_ids' => ['sometimes', 'array'],
            'assignee_ids.*' => ['string', 'exists:users,id'],
        ];
    }
}
