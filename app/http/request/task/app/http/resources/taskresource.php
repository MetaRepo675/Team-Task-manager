<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => [
                'value' => $this->status,
                'label' => __('tasks.status.' . $this->status),
            ],
            'priority' => [
                'value' => $this->priority,
                'label' => __('tasks.priority.' . $this->priority),
            ],
            'due_date' => $this->due_date?->toIso8601String(),
            'due_date_formatted' => $this->due_date?->toJalali()->format('Y/m/d'),
            'created_at' => $this->created_at->toIso8601String(),
            'creator' => UserResource::make($this->whenLoaded('creator')),
            'assignee' => UserResource::make($this->whenLoaded('assignee')),
        ];
    }
}
