<?php

namespace App\Services;

use App\Models\KanbanColumn;

class KanbanColumnService
{
    public function list(string $projectId)
    {
        return KanbanColumn::where('project_id', $projectId)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();
    }

    public function create(string $projectId, array $data): KanbanColumn
    {
        $maxPosition = KanbanColumn::where('project_id', $projectId)->max('position') ?? -1;

        return KanbanColumn::create([
            'project_id' => $projectId,
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'wip_limit' => $data['wip_limit'] ?? null,
            'position' => $maxPosition + 1,
        ]);
    }

    public function update(KanbanColumn $column, array $data): KanbanColumn
    {
        $column->update($data);
        return $column->fresh();
    }

    public function delete(KanbanColumn $column): void
    {
        $column->update(['is_active' => false]);
    }

    public function reorder(array $columns): void
    {
        foreach ($columns as $item) {
            KanbanColumn::where('id', $item['id'])->update(['position' => $item['position']]);
        }
    }
}
