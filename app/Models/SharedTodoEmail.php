<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedTodoEmail extends Model
{
    protected $fillable = ['shared_todo_id', 'email'];

    public function sharedTodo()
    {
        return $this->belongsTo(SharedTodo::class);
    }
}
