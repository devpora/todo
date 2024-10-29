<?php

namespace App\Http\Resources;

use App\Models\SharedTodo;
use App\Models\Todo;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoListSharedResource extends JsonResource
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

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'sharedLink' => $isShared ? $sharedTodo->getShareLink() : null,
        ];
    }
}
