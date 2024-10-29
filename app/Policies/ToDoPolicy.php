<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

class ToDoPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Todo $todo): bool
    {
        return $user->getId() === $todo->getUserId();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Todo $todo): bool
    {
        return $user->getId() === $todo->getUserId();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Todo $todo): bool
    {
        return $user->getId() === $todo->getUserId();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Todo $todo): bool
    {
        return $user->getId() === $todo->getUserId();
    }
}
