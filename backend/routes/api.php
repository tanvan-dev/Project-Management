<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\KanbanColumnController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SprintController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    Route::apiResource('workspaces', WorkspaceController::class);
    Route::get('/workspaces/{workspace}/members', [WorkspaceController::class, 'members']);
    Route::post('/workspaces/{workspace}/members', [WorkspaceController::class, 'addMember']);
    Route::delete('/workspaces/{workspace}/members/{user}', [WorkspaceController::class, 'removeMember']);

    Route::get('/workspaces/{workspace}/projects', [ProjectController::class, 'index']);
    Route::post('/workspaces/{workspace}/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::patch('/projects/{project}', [ProjectController::class, 'update']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    Route::patch('/projects/{project}/archive', [ProjectController::class, 'archive']);
    Route::patch('/projects/{project}/restore', [ProjectController::class, 'restore']);
    Route::get('/projects/{project}/members', [ProjectController::class, 'members']);
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember']);
    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'removeMember']);

    Route::get('/projects/{project}/sprints', [SprintController::class, 'index']);
    Route::post('/projects/{project}/sprints', [SprintController::class, 'store']);
    Route::get('/sprints/{sprint}', [SprintController::class, 'show']);
    Route::patch('/sprints/{sprint}', [SprintController::class, 'update']);
    Route::put('/sprints/{sprint}', [SprintController::class, 'update']);
    Route::delete('/sprints/{sprint}', [SprintController::class, 'destroy']);
    Route::post('/sprints/{sprint}/start', [SprintController::class, 'start']);
    Route::post('/sprints/{sprint}/complete', [SprintController::class, 'complete']);

    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::patch('/tasks/{task}', [TaskController::class, 'update']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/tasks/{task}/reorder', [TaskController::class, 'reorder']);
    Route::post('/tasks/{task}/assign', [TaskController::class, 'assign']);

    Route::get('/projects/{project}/kanban-columns', [KanbanColumnController::class, 'index']);
    Route::post('/projects/{project}/kanban-columns', [KanbanColumnController::class, 'store']);
    Route::get('/kanban-columns/{kanbanColumn}', [KanbanColumnController::class, 'show']);
    Route::patch('/kanban-columns/{kanbanColumn}', [KanbanColumnController::class, 'update']);
    Route::put('/kanban-columns/{kanbanColumn}', [KanbanColumnController::class, 'update']);
    Route::delete('/kanban-columns/{kanbanColumn}', [KanbanColumnController::class, 'destroy']);
    Route::post('/kanban-columns/reorder', [KanbanColumnController::class, 'reorder']);
});
