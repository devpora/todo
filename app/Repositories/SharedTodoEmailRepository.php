<?php

namespace App\Repositories;

use App\Models\SharedTodoEmail;

class SharedTodoEmailRepository
{
    public function getEmailsBySharedTodoId($sharedTodoId)
    {
        return SharedTodoEmail::where('shared_todo_id', $sharedTodoId)->pluck('email')->toArray();
    }

    public function deleteBySharedTodoId($sharedTodoId)
    {
        SharedTodoEmail::where('shared_todo_id', $sharedTodoId)->delete();
    }

    public function addEmails($sharedTodo, array $emails)
    {
        foreach ($emails as $email) {
            $sharedTodo->emails()->create(['email' => $email]);
        }
    }
}
