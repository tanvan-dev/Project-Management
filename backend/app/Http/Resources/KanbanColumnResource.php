<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KanbanColumnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'wip_limit' => $this->wip_limit,
            'position' => $this->position,
            'is_active' => $this->is_active,
            'tasks_count' => $this->tasks_count,
            'is_wip_limit_reached' => $this->is_wip_limit_reached,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
