<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KanbanColumn\ReorderKanbanColumnRequest;
use App\Http\Requests\KanbanColumn\StoreKanbanColumnRequest;
use App\Http\Requests\KanbanColumn\UpdateKanbanColumnRequest;
use App\Http\Resources\KanbanColumnResource;
use App\Models\KanbanColumn;
use App\Models\Project;
use App\Services\KanbanColumnService;
use Illuminate\Http\JsonResponse;

class KanbanColumnController extends Controller
{
    public function __construct(
        private readonly KanbanColumnService $kanbanColumnService
    ) {}

    public function index(Project $project): JsonResponse
    {
        $columns = $this->kanbanColumnService->list($project->id);

        return response()->json(KanbanColumnResource::collection($columns));
    }

    public function store(StoreKanbanColumnRequest $request, Project $project): JsonResponse
    {
        $column = $this->kanbanColumnService->create($project->id, $request->validated());

        return response()->json(new KanbanColumnResource($column), 201);
    }

    public function show(KanbanColumn $kanbanColumn): JsonResponse
    {
        return response()->json(new KanbanColumnResource($kanbanColumn));
    }

    public function update(UpdateKanbanColumnRequest $request, KanbanColumn $kanbanColumn): JsonResponse
    {
        $column = $this->kanbanColumnService->update($kanbanColumn, $request->validated());

        return response()->json(new KanbanColumnResource($column));
    }

    public function destroy(KanbanColumn $kanbanColumn): JsonResponse
    {
        $this->kanbanColumnService->delete($kanbanColumn);

        return response()->json(null, 204);
    }

    public function reorder(ReorderKanbanColumnRequest $request): JsonResponse
    {
        $this->kanbanColumnService->reorder($request->input('columns'));

        return response()->json(['message' => 'Columns reordered successfully']);
    }
}
