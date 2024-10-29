<?php

namespace App\Http\Resources;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoDeletedResource extends JsonResource
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
            'deleted_at' => Carbon::parse( $this->getDeletedAt())->toDateTimeString()
        ];
    }
}

