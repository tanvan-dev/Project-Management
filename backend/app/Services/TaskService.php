<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAssignee;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function list(string $projectId, array $filters = [])
    {
        return Task::where('project_id', $projectId)
            ->with(['creator', 'assignees', 'sprint'])
            ->when($filters['sprint_id'] ?? null, fn($q, $v) => $q->where('sprint_id', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['priority'] ?? null, fn($q, $v) => $q->where('priority', $v))
            ->when($filters['assignee_id'] ?? null, fn($q, $v) => $q->whereHas('assignees', fn($q) => $q->where('user_id', $v)))
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->when($filters['overdue'] ?? null, fn($q) => $q->overdue())
            ->orderBy('position')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(string $projectId, array $data): Task
    {
        return DB::transaction(function () use ($projectId, $data) {
            $task = Task::create([
                'project_id' => $projectId,
                'sprint_id' => $data['sprint_id'] ?? null,
                'parent_task_id' => $data['parent_task_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'todo',
                'priority' => $data['priority'] ?? 'medium',
                'due_date' => $data['due_date'] ?? null,
                'estimated_hours' => $data['estimated_hours'] ?? null,
                'position' => $data['position'] ?? 0,
                'created_by' => auth('sanctum')->id(),
            ]);

            if (!empty($data['assignee_ids'])) {
                foreach ($data['assignee_ids'] as $userId) {
                    TaskAssignee::create([
                        'task_id' => $task->id,
                        'user_id' => $userId,
                        'assigned_at' => now(),
                    ]);
                }
            }

            return $task->load(['creator', 'assignees', 'sprint']);
        });
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh(['creator', 'assignees', 'sprint', 'checklists', 'subtaskItems']);
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function reorder(Task $task, array $data): Task
    {
        $task->update([
            'status' => $data['status'] ?? $task->status,
            'position' => $data['position'] ?? $task->position,
            'sprint_id' => $data['sprint_id'] ?? $task->sprint_id,
        ]);
        return $task->fresh();
    }

    public function assignUsers(Task $task, array $userIds): Task
    {
        TaskAssignee::where('task_id', $task->id)->delete();

        foreach ($userIds as $userId) {
            TaskAssignee::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'assigned_at' => now(),
            ]);
        }

        return $task->fresh(['assignees']);
    }
}
