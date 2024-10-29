<?php

namespace App\Http\Controllers;

use App\Http\Requests\Todo\CompletedRequest;
use App\Http\Requests\Todo\CreateRequest;
use App\Http\Requests\Todo\UpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Todo;
use App\Repositories\CategoryRepository;
use App\Repositories\SharedTodoEmailRepository;
use App\Repositories\SharedTodoRepository;
use App\Repositories\TodoRepository;
use App\Services\TodoService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class TodoController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private TodoRepository $todoRepository,
        private SharedTodoRepository $sharedTodoRepository,
        private SharedTodoEmailRepository $sharedTodoEmailRepository,
        private CategoryRepository $categoryRepository,
        private TodoService $todoService,
    ) {}

    public function index()
    {
        $categories = $this->categoryRepository->getAllCategories();

        return Inertia::render('Dashboard', [
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    public function quickStore(CreateRequest $request)
    {
        $this->todoService->createTodo($request->getName(), auth()->id());

        return redirect()
            ->route('dashboard')
            ->with('success', 'Quick ToDo created successfully!');
    }

    public function update(UpdateRequest $request, Todo $todo)
    {
        $this->authorize('update', $todo);

        $this->todoService->updateTodoWithSharing($todo, $request);

        return redirect()->back()->with('status', 'ToDo updated successfully!');
    }

    public function completed(CompletedRequest $request, Todo $todo)
    {
        $this->authorize('update', $todo);

        $this->todoService->setAsCompleted($todo, $request);

        return redirect()->back()->with('status', 'Todo updated');
    }

    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);

        $this->todoService->destroy($todo);

        return redirect()->route('dashboard')->with('success', 'ToDo deleted successfully!');
    }

    public function forceDestroy(Todo $todoWithTrashed)
    {
        $this->authorize('delete', $todoWithTrashed);

        $this->todoService->forceDestroy($todoWithTrashed);

        return redirect()->route('dashboard')->with('success', 'ToDo deleted successfully!');
    }

    public function restore(Todo $todo)
    {
        $this->authorize('restore', $todo);

        $this->todoService->restore($todo);

        return redirect()->route('dashboard')->with('success', 'ToDo restored successfully!');
    }
}
