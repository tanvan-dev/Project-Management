<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\ReorderTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    public function index(Project $project): JsonResponse
    {
        $tasks = $this->taskService->list($project->id, request()->only([
            'sprint_id', 'status', 'priority', 'assignee_id', 'search', 'overdue', 'per_page',
        ]));

        return response()->json(TaskResource::collection($tasks)->response()->getData(true));
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $task = $this->taskService->create($project->id, $request->validated());

        return response()->json(new TaskResource($task), 201);
    }

    public function show(Task $task): JsonResponse
    {
        $task->load(['creator', 'assignees', 'sprint', 'checklists', 'subtaskItems', 'attachments']);

        return response()->json(new TaskResource($task));
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->update($task, $request->validated());

        return response()->json(new TaskResource($task));
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task);

        return response()->json(null, 204);
    }

    public function reorder(ReorderTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->reorder($task, $request->validated());

        return response()->json(new TaskResource($task));
    }

    public function assign(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'assignee_ids' => ['required', 'array'],
            'assignee_ids.*' => ['string', 'exists:users,id'],
        ]);

        $task = $this->taskService->assignUsers($task, $request->input('assignee_ids'));

        return response()->json(new TaskResource($task));
    }
}
