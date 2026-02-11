<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private TaskRepositoryInterface $taskRepository
    ) {
        $this->authorizeResource(Task::class, 'task');
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'priority', 'search', 'per_page']);
        $tasks = $this->taskRepository->getAllForUser(auth()->id(), $filters);

        return response()->json([
            'data' => TaskResource::collection($tasks),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'total' => $tasks->total(),
            ]
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());

        return response()->json([
            'message' => 'Task created successfully',
            'data' => TaskResource::make($task)
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'data' => TaskResource::make($task->load(['creator', 'assignee']))
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->updateTask($task, $request->validated());

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => TaskResource::make($task)
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
