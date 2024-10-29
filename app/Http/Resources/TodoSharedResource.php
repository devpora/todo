<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'is_completed' => (bool) $this->is_completed,
            'deleted_at' => $this->deleted_at,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
