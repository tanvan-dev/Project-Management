<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SprintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'goal' => $this->goal,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'duration_days' => $this->when($this->start_date && $this->end_date, fn() => $this->duration_days),
            'progress' => $this->whenLoaded('tasks', fn() => $this->progress),
            'tasks_count' => $this->whenCounted('tasks'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
