<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\KanbanColumn;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function list(string $workspaceId, array $filters = [])
    {
        return Project::where('workspace_id', $workspaceId)
            ->with('owner')
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when(array_key_exists('is_archived', $filters), fn($q) => $q->where('is_archived', $filters['is_archived']))
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(string $workspaceId, array $data): Project
    {
        return DB::transaction(function () use ($workspaceId, $data) {
            $project = Project::create([
                'workspace_id' => $workspaceId,
                'owner_id' => auth('sanctum')->id(),
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'methodology' => $data['methodology'] ?? 'scrum',
                'settings' => $data['settings'] ?? null,
            ]);

            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => auth('sanctum')->id(),
                'role' => 'owner',
                'joined_at' => now(),
            ]);

            if ($project->methodology === 'kanban') {
                $columns = [
                    ['name' => 'todo', 'display_name' => 'To Do', 'position' => 0],
                    ['name' => 'in_progress', 'display_name' => 'In Progress', 'position' => 1],
                    ['name' => 'in_review', 'display_name' => 'In Review', 'position' => 2],
                    ['name' => 'done', 'display_name' => 'Done', 'position' => 3],
                ];
                foreach ($columns as $col) {
                    KanbanColumn::create(array_merge($col, ['project_id' => $project->id]));
                }
            }

            return $project->load(['owner', 'kanbanColumns']);
        });
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);
        return $project->fresh(['owner', 'kanbanColumns']);
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }

    public function archive(Project $project): Project
    {
        $project->update(['is_archived' => true]);
        return $project->fresh();
    }

    public function restore(Project $project): Project
    {
        $project->update(['is_archived' => false]);
        return $project->fresh();
    }

    public function addMember(Project $project, array $data): ProjectMember
    {
        return ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $data['user_id'],
            'role' => $data['role'] ?? 'member',
            'joined_at' => now(),
        ]);
    }

    public function removeMember(Project $project, string $userId): void
    {
        $project->projectMembers()->where('user_id', $userId)->delete();
    }

    public function getMembers(Project $project)
    {
        return $project->members()->withPivot('role', 'joined_at')->get();
    }
}
