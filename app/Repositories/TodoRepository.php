<?php

namespace App\Repositories;

use App\Models\SharedTodo;
use App\Models\Todo;

class TodoRepository
{
    protected $sharedTodoRepository;

    protected $sharedTodoEmailRepository;

    public function __construct(SharedTodoRepository $sharedTodoRepository, SharedTodoEmailRepository $sharedTodoEmailRepository)
    {
        $this->sharedTodoRepository = $sharedTodoRepository;
        $this->sharedTodoEmailRepository = $sharedTodoEmailRepository;
    }

    public function create(array $data)
    {
        return Todo::create($data);
    }

    public function update(Todo $todo, array $data)
    {
        $todo->update($data);

        return $todo;
    }

    public function delete(Todo $todo)
    {
        $todo->delete();
    }

    public function forceDelete(Todo $todo)
    {
        $todo->forceDelete();
    }

    public function restore(Todo $todo)
    {
        $todo->restore();
    }

    public function findByUserAndId($userId, $todoId)
    {
        return Todo::where('user_id', $userId)->findOrFail($todoId);
    }

    public function findWithTrashedByUserAndId($userId, $todoId)
    {
        return Todo::where('user_id', $userId)->withTrashed()->findOrFail($todoId);
    }

    public function getUserTodos($user)
    {
        return $user->todos()->orderBy('updated_at', 'DESC');
    }

    public function getPrivateTodos($email)
    {
        return Todo::orderBy('updated_at', 'DESC')
            ->whereHas('sharedTodo.emails', function ($query) use ($email) {
                $query->where('email', $email);
            });
    }

    public function getPublicTodo($slug)
    {
        /** @var SharedTodo $sharedTodo */
        $sharedTodo = SharedTodo::where('share_link', $slug)->firstOrFail();

        return Todo::with('categories')->findOrFail($sharedTodo->getTodoId());
    }

    public function getPrivateTodo($slug, $email)
    {
        /** @var SharedTodo $sharedTodoId */
        $sharedTodoId = $this->sharedTodoRepository->findBySlug($slug);
        $sharedEmails = $this->sharedTodoEmailRepository->getEmailsBySharedTodoId($sharedTodoId->getId());

        if (in_array($email, $sharedEmails)) {
            return Todo::where('id', $sharedTodoId->getTodoId())
                ->with(['categories', 'sharedTodo.emails'])
                ->firstOrFail();
        }

        abort(403, 'You do not have access to this Todo.');
    }
}
