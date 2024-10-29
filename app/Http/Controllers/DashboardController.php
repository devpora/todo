<?php

namespace App\Http\Controllers;

use App\Http\Requests\Todo\QueryRequest;
use App\Http\Resources\TodoDeletedResource;
use App\Http\Resources\TodoListResource;
use App\Http\Resources\TodoResource;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\SharedTodoEmailRepository;
use App\Repositories\SharedTodoRepository;
use App\Repositories\TodoRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;
    private $todoRepository;
    private $sharedTodoRepository;
    private $sharedTodoEmailRepository;
    private $categoryRepository;

    public function __construct(TodoRepository $todoRepository, SharedTodoRepository $sharedTodoRepository, SharedTodoEmailRepository $sharedTodoEmailRepository, CategoryRepository $categoryRepository)
    {
        $this->todoRepository = $todoRepository;
        $this->sharedTodoRepository = $sharedTodoRepository;
        $this->sharedTodoEmailRepository = $sharedTodoEmailRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getActive(QueryRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();

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

        $activeTodos = $query->paginate(5);

        return TodoResource::collection($activeTodos);
    }
    public function getShared()
    {
        /** @var User $user */
        $user = auth()->user();

        $sharedTodos = $this->todoRepository
            ->getPrivateTodos($user->getEmail())
            ->paginate(5);

        return TodoListResource::collection($sharedTodos);
    }

    public function getCompleted()
    {
        /** @var User $user */
        $user = auth()->user();

        $sharedTodos = $this->todoRepository
            ->getUserTodos($user)
            ->where('is_completed', true)
            ->whereNull('deleted_at')
            ->paginate(5);

        return TodoListResource::collection($sharedTodos);
    }

    public function getDeleted()
    {
        /** @var User $user */
        $user = auth()->user();

        $sharedTodos = $this->todoRepository
            ->getUserTodos($user)
            ->onlyTrashed()
            ->paginate(5);

        return TodoDeletedResource::collection($sharedTodos);
    }
}
