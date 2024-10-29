<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedTodoEmail extends Model
{
    protected $fillable = ['shared_todo_id', 'email'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function sharedTodo()
    {
        return $this->belongsTo(SharedTodo::class);
    }
}
