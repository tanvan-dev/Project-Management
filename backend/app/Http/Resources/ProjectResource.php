<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workspace_id' => $this->workspace_id,
            'name' => $this->name,
            'description' => $this->description,
            'methodology' => $this->methodology,
            'owner_id' => $this->owner_id,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'settings' => $this->settings,
            'is_archived' => $this->is_archived,
            'members' => UserResource::collection($this->whenLoaded('members')),
            'kanban_columns' => KanbanColumnResource::collection($this->whenLoaded('kanbanColumns')),
            'sprints_count' => $this->whenCounted('sprints'),
            'tasks_count' => $this->whenCounted('tasks'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
