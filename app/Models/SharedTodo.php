<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedTodo extends Model
{
    protected $fillable = ['todo_id', 'share_link', 'is_public'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getTodoId(): int
    {
        return $this->todo_id;
    }

    public function getShareLink(): ?string
    {
        return $this->share_link;
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function emails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SharedTodoEmail::class);
    }
}
