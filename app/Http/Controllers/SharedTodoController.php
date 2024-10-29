<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoSharedResource;
use App\Models\User;
use App\Repositories\TodoRepository;
use Inertia\Inertia;

class SharedTodoController extends Controller
{
    public function __construct(
        protected TodoRepository $todoRepository
    ) {}

    public function showPublic($slug)
    {
        $todo = $this->todoRepository->getPublicTodo($slug);

        return Inertia::render('SharedTodo', [
            'todo' => new TodoSharedResource($todo),
        ]);
    }

    public function showPrivate($slug)
    {
        /** @var User $user */
        $user = auth()->user();
        $todo = $this->todoRepository->getPrivateTodo($slug, $user->getEmail());

        return Inertia::render('SharedTodo', [
            'todo' => new TodoSharedResource($todo),
        ]);
    }
}
