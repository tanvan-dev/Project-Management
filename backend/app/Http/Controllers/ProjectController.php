<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\AddProjectMemberRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    public function index(Workspace $workspace): JsonResponse
    {
        $projects = $this->projectService->list($workspace->id, request()->only(['search', 'is_archived', 'per_page']));

        return response()->json(ProjectResource::collection($projects)->response()->getData(true));
    }

    public function store(StoreProjectRequest $request, Workspace $workspace): JsonResponse
    {
        $project = $this->projectService->create($workspace->id, $request->validated());

        return response()->json(new ProjectResource($project), 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['owner', 'members', 'kanbanColumns']);

        return response()->json(new ProjectResource($project));
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project = $this->projectService->update($project, $request->validated());

        return response()->json(new ProjectResource($project));
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->projectService->delete($project);

        return response()->json(null, 204);
    }

    public function archive(Project $project): JsonResponse
    {
        $project = $this->projectService->archive($project);

        return response()->json(new ProjectResource($project));
    }

    public function restore(Project $project): JsonResponse
    {
        $project = $this->projectService->restore($project);

        return response()->json(new ProjectResource($project));
    }

    public function members(Project $project): JsonResponse
    {
        $members = $this->projectService->getMembers($project);

        return response()->json(UserResource::collection($members));
    }

    public function addMember(AddProjectMemberRequest $request, Project $project): JsonResponse
    {
        $this->projectService->addMember($project, $request->validated());

        return response()->json(['message' => 'Member added successfully'], 201);
    }

    public function removeMember(Project $project, User $user): JsonResponse
    {
        $this->projectService->removeMember($project, $user->id);

        return response()->json(null, 204);
    }
}
