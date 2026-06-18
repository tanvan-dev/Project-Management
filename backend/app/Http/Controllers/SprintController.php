<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sprint\StoreSprintRequest;
use App\Http\Requests\Sprint\UpdateSprintRequest;
use App\Http\Resources\SprintResource;
use App\Models\Project;
use App\Models\Sprint;
use App\Services\SprintService;
use Illuminate\Http\JsonResponse;

class SprintController extends Controller
{
    public function __construct(
        private readonly SprintService $sprintService
    ) {}

    public function index(Project $project): JsonResponse
    {
        $sprints = $this->sprintService->list($project->id, request()->only(['status', 'per_page']));

        return response()->json(SprintResource::collection($sprints)->response()->getData(true));
    }

    public function store(StoreSprintRequest $request, Project $project): JsonResponse
    {
        $sprint = $this->sprintService->create($project->id, $request->validated());

        return response()->json(new SprintResource($sprint), 201);
    }

    public function show(Sprint $sprint): JsonResponse
    {
        $sprint->loadCount('tasks');

        return response()->json(new SprintResource($sprint));
    }

    public function update(UpdateSprintRequest $request, Sprint $sprint): JsonResponse
    {
        $sprint = $this->sprintService->update($sprint, $request->validated());

        return response()->json(new SprintResource($sprint));
    }

    public function destroy(Sprint $sprint): JsonResponse
    {
        $this->sprintService->delete($sprint);

        return response()->json(null, 204);
    }

    public function start(Sprint $sprint): JsonResponse
    {
        $sprint = $this->sprintService->start($sprint);

        return response()->json(new SprintResource($sprint));
    }

    public function complete(Sprint $sprint): JsonResponse
    {
        $sprint = $this->sprintService->complete($sprint);

        return response()->json(new SprintResource($sprint));
    }
}
