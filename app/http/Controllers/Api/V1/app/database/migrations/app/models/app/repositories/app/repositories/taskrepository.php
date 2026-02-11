<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllForUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Task::where('user_id', $userId)
            ->orWhere('assigned_to', $userId);

        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->priority($filters['priority']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): ?Task
    {
        return Cache::remember("task.{$id}", 3600, function () use ($id) {
            return Task::find($id);
        });
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        Cache::forget("task.{$task->id}");
        
        return $task;
    }

    public function delete(Task $task): bool
    {
        Cache::forget("task.{$task->id}");
        return $task->delete();
    }
}
