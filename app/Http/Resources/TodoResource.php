<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'is_completed' => (bool) $this->is_completed,
            'isShared' => (bool) $this->sharedTodo,
            'isPublic' => $this->sharedTodo ? (bool) $this->sharedTodo->is_public : false,
            'sharedLink' => $this->sharedTodo ? $this->sharedTodo->share_link : null,
            'categories' => CategoryResource::collection($this->categories),
            'emails' => $this->sharedTodo
                ? $this->sharedTodo->emails->take(5)->map(function ($email) {
                    return ['id' => $email->id, 'email' => $email->email];
                })
                : null,
            'sharedEmails' => $this->sharedTodo
                ? $this->sharedTodo->emails->map(function ($email) {
                    return $email->email;
                })
                : [],
            'userCounter' => $this->sharedTodo && $this->sharedTodo->emails->count() > 5
                ? $this->sharedTodo->emails->count() - 5
                : null,
        ];
    }
}
