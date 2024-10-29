<?php

namespace App\Repositories;

use App\Models\SharedTodo;

class SharedTodoRepository
{
    public function findByTodoId($todoId)
    {
        return SharedTodo::where('todo_id', $todoId)->first();
    }

    public function findBySlug($slug)
    {
        return SharedTodo::where('share_link', $slug)->firstOrFail();
    }

    public function create(array $data)
    {
        return SharedTodo::create($data);
    }

    public function update(SharedTodo $sharedTodo, array $data)
    {
        $sharedTodo->update($data);

        return $sharedTodo;
    }

    public function delete(SharedTodo $sharedTodo)
    {
        $sharedTodo->delete();
    }
}
