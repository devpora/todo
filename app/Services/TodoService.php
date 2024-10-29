<?php

namespace App\Services;

use App\Http\Requests\Todo\QueryRequest;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\TodoRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class TodoService{

    public function __construct(
        private TodoRepository $todoRepository,
        private CategoryRepository $categoryRepository,
    ) {}

    public function getActiveTodos(User $user, QueryRequest $request): LengthAwarePaginator
    {
        $query = $this->todoRepository->getUserTodos($user)
            ->where('is_completed', false)
            ->whereNull('deleted_at');

        if ($request->getCategory()) {
            $categories = explode(',', $request->getCategory());
            $categoryIds = $this->categoryRepository->getIdsByName($categories);
            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        if (!is_null($request->getShared())) {
            $shared = filter_var($request->getShared(), FILTER_VALIDATE_BOOLEAN);
            if ($shared) {
                $query->whereHas('sharedTodo');
            } else {
                $query->whereDoesntHave('sharedTodo');
            }
        }

        if ($request->getName()) {
            $name = $request->getName();
            $query->where(function ($q) use ($name) {
                $q->where('name', 'like', "%{$name}%");
            });
        }
        if ($request->getDescription()) {
            $description = $request->getDescription();
            $query->where(function ($q) use ($description) {
                $q->where('description', 'like', "%{$description}%");
            });
        }

        return $query->paginate(5);
    }

    public function getSharedTodos(User $user): LengthAwarePaginator
    {
        return $this->todoRepository
            ->getPrivateTodos($user->getEmail())
            ->paginate(5);
    }

    public function getCompleted(User $user): LengthAwarePaginator
    {
        return $this->todoRepository
            ->getUserTodos($user)
            ->where('is_completed', true)
            ->whereNull('deleted_at')
            ->paginate(5);
    }

    public function getDeleted(User $user): LengthAwarePaginator
    {
        return $this->todoRepository
            ->getUserTodos($user)
            ->onlyTrashed()
            ->paginate(5);
    }

}
