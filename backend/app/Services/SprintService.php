<?php

namespace App\Services;

use App\Models\Sprint;

class SprintService
{
    public function list(string $projectId, array $filters = [])
    {
        return Sprint::where('project_id', $projectId)
            ->withCount('tasks')
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(string $projectId, array $data): Sprint
    {
        return Sprint::create([
            'project_id' => $projectId,
            'name' => $data['name'],
            'goal' => $data['goal'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => 'planning',
        ]);
    }

    public function update(Sprint $sprint, array $data): Sprint
    {
        $sprint->update($data);
        return $sprint->fresh()->loadCount('tasks');
    }

    public function delete(Sprint $sprint): void
    {
        $sprint->delete();
    }

    public function start(Sprint $sprint): Sprint
    {
        $sprint->update([
            'status' => 'active',
            'start_date' => $sprint->start_date ?? now(),
        ]);
        return $sprint->fresh()->loadCount('tasks');
    }

    public function complete(Sprint $sprint): Sprint
    {
        $sprint->update([
            'status' => 'completed',
            'end_date' => $sprint->end_date ?? now(),
        ]);
        return $sprint->fresh()->loadCount('tasks');
    }
}
