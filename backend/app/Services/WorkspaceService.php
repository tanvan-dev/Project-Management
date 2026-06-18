<?php

namespace App\Services;

use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Support\Facades\DB;

class WorkspaceService
{
    public function list(array $filters = [])
    {
        return Workspace::where('owner_id', auth('sanctum')->id())
            ->orWhereHas('members', fn($q) => $q->where('user_id', auth('sanctum')->id()))
            ->with('owner')
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Workspace
    {
        return DB::transaction(function () use ($data) {
            $workspace = Workspace::create([
                'owner_id' => auth('sanctum')->id(),
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'settings' => $data['settings'] ?? null,
            ]);

            WorkspaceMember::create([
                'workspace_id' => $workspace->id,
                'user_id' => auth('sanctum')->id(),
                'role' => 'owner',
                'joined_at' => now(),
            ]);

            return $workspace->load('owner');
        });
    }

    public function update(Workspace $workspace, array $data): Workspace
    {
        $workspace->update($data);
        return $workspace->fresh(['owner']);
    }

    public function delete(Workspace $workspace): void
    {
        $workspace->delete();
    }

    public function addMember(Workspace $workspace, array $data): WorkspaceMember
    {
        $member = WorkspaceMember::create([
            'workspace_id' => $workspace->id,
            'user_id' => $data['user_id'],
            'role' => $data['role'] ?? 'member',
            'joined_at' => now(),
        ]);

        return $member;
    }

    public function removeMember(Workspace $workspace, string $userId): void
    {
        $workspace->workspaceMembers()->where('user_id', $userId)->delete();
    }

    public function getMembers(Workspace $workspace)
    {
        return $workspace->members()->withPivot('role', 'joined_at')->get();
    }
}
