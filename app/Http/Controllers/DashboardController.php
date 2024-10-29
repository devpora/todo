<?php

namespace App\Http\Controllers;

use App\Http\Requests\Todo\QueryRequest;
use App\Http\Resources\TodoDeletedResource;
use App\Http\Resources\TodoListResource;
use App\Http\Resources\TodoResource;
use App\Models\User;
use App\Services\TodoService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private TodoService $todoService,
    ) {}

    public function getActive(QueryRequest $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = auth()->user();

        $activeTodos = $this->todoService->getActiveTodos($user, $request);

        return TodoResource::collection($activeTodos);
    }

    public function getShared(): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = auth()->user();
        $sharedTodos = $this->todoService->getSharedTodos($user);

        return TodoListResource::collection($sharedTodos);
    }

    public function getCompleted(): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = auth()->user();

        $sharedTodos = $this->todoService->getCompleted($user);

        return TodoListResource::collection($sharedTodos);
    }

    public function getDeleted(): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = auth()->user();

        $sharedTodos = $this->todoService->getDeleted($user);

        return TodoDeletedResource::collection($sharedTodos);
    }
}
