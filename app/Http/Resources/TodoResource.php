<?php

namespace App\Http\Resources;

use App\Models\SharedTodo;
use App\Models\SharedTodoEmail;
use App\Models\Todo;
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
        /** @var Todo $this */
        $isShared = (bool) $this->sharedTodo()->getResults();
        /** @var SharedTodo|null $sharedTodo */
        $sharedTodo = $this->sharedTodo()->getResults();
        $emailsCollection = $sharedTodo ? $sharedTodo->emails()->get() : collect();

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'is_completed' => $this->isCompleted(),
            'isShared' => $isShared,
            'isPublic' => $isShared && $sharedTodo->isPublic(),
            'sharedLink' => $isShared ? $sharedTodo->getShareLink() : null,
            'categories' => CategoryResource::collection($this->categories()->getResults()),
            'emails' => $emailsCollection->take(5)->map(function ($email) {
                /** @var SharedTodoEmail $email */
                return [
                    'id' => $email->getId(),
                    'email' => $email->getEmail(),
                ];
            }),
            'sharedEmails' => $emailsCollection->map(function ($email) {
                return $email->email;
            }),
            'userCounter' => $emailsCollection->count() > 5
                ? $emailsCollection->count() - 5
                : null,
        ];
    }
}
