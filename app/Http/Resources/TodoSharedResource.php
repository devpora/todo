<?php

namespace App\Http\Resources;

use App\Models\Todo;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoSharedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        /** @var Todo $this */
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'is_completed' => $this->isCompleted(),
            'deleted_at' => $this->getDeletedAt(),
            'categories' => $this->relationLoaded('categories')
                ? CategoryResource::collection($this->categories()->getResults())
                : null,
        ];
    }
}
