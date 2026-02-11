<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Services\TaskService;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'priority', 'search']);
        $tasks = $this->taskRepository->getAllForUser(auth()->id(), $filters);

        return view('tasks.index', compact('tasks', 'filters'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask($request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'تسک با موفقیت ایجاد شد.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        
        return view('tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $task = $this->taskService->updateTask($task, $request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'تسک با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $this->taskService->deleteTask($task);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'تسک با موفقیت حذف شد.');
    }
}
