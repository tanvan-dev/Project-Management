<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\AddWorkspaceMemberRequest;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\JsonResponse;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly WorkspaceService $workspaceService
    ) {}

    public function index(): JsonResponse
    {
        $workspaces = $this->workspaceService->list(request()->only(['search', 'per_page']));

        return response()->json(WorkspaceResource::collection($workspaces)->response()->getData(true));
    }

    public function store(StoreWorkspaceRequest $request): JsonResponse
    {
        $workspace = $this->workspaceService->create($request->validated());

        return response()->json(new WorkspaceResource($workspace), 201);
    }

    public function show(Workspace $workspace): JsonResponse
    {
        $workspace->load(['owner', 'members']);

        return response()->json(new WorkspaceResource($workspace));
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): JsonResponse
    {
        $workspace = $this->workspaceService->update($workspace, $request->validated());

        return response()->json(new WorkspaceResource($workspace));
    }

    public function destroy(Workspace $workspace): JsonResponse
    {
        $this->workspaceService->delete($workspace);

        return response()->json(null, 204);
    }

    public function members(Workspace $workspace): JsonResponse
    {
        $members = $this->workspaceService->getMembers($workspace);

        return response()->json(UserResource::collection($members));
    }

    public function addMember(AddWorkspaceMemberRequest $request, Workspace $workspace): JsonResponse
    {
        $this->workspaceService->addMember($workspace, $request->validated());

        return response()->json(['message' => 'Member added successfully'], 201);
    }

    public function removeMember(Workspace $workspace, User $user): JsonResponse
    {
        $this->workspaceService->removeMember($workspace, $user->id);

        return response()->json(null, 204);
    }
}
