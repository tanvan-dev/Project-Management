<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'sprint_id' => $this->sprint_id,
            'parent_task_id' => $this->parent_task_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'estimated_hours' => $this->estimated_hours,
            'actual_hours' => $this->actual_hours,
            'position' => $this->position,
            'is_overdue' => $this->is_overdue,
            'checklist_progress' => $this->whenLoaded('checklists', fn() => $this->checklist_progress),
            'subtasks_progress' => $this->whenLoaded('subtaskItems', fn() => $this->subtasks_progress),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'assignees' => UserResource::collection($this->whenLoaded('assignees')),
            'sprint' => new SprintResource($this->whenLoaded('sprint')),
            'checklists' => $this->whenLoaded('checklists'),
            'subtask_items' => $this->whenLoaded('subtaskItems'),
            'attachments' => $this->whenLoaded('attachments'),
            'comments_count' => $this->whenCounted('comments'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
