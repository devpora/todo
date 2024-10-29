<?php

namespace App\Http\Resources;

use App\Models\Todo;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoListResource extends JsonResource
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
            'name' => $this->getName(),
        ];
    }
}
