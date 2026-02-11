<?php

namespace App\Services;

use App\Repositories\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}

    public function createTask(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task = $this->taskRepository->create($data);
            
            Log::info('Task created', [
                'task_id' => $task->id,
                'user_id' => $data['user_id']
            ]);

            return $task;
        });
    }

    public function updateTask(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $updatedTask = $this->taskRepository->update($task, $data);
            
            Log::info('Task updated', [
                'task_id' => $task->id,
                'user_id' => auth()->id()
            ]);

            return $updatedTask;
        });
    }

    public function deleteTask(Task $task): bool
    {
        return DB::transaction(function () use ($task) {
            $result = $this->taskRepository->delete($task);
            
            Log::info('Task deleted', [
                'task_id' => $task->id,
                'user_id' => auth()->id()
            ]);

            return $result;
        });
    }
}
