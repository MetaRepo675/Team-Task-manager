<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Task;

interface TaskRepositoryInterface
{
    public function getAllForUser(int $userId, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Task;
    public function create(array $data): Task;
    public function update(Task $task, array $data): Task;
    public function delete(Task $task): bool;
}
