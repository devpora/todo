<?php

namespace App\Http\Controllers;

use App\Http\Requests\Todo\CompletedRequest;
use App\Http\Requests\Todo\CreateRequest;
use App\Http\Requests\Todo\QueryRequest;
use App\Http\Requests\Todo\UpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use App\Models\User;
use App\Notifications\TodoNotification;
use App\Notifications\TodoSharedNotification;
use App\Repositories\CategoryRepository;
use App\Repositories\SharedTodoEmailRepository;
use App\Repositories\SharedTodoRepository;
use App\Repositories\TodoRepository;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TodoController extends Controller
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

    public function index(QueryRequest $request)
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

        $activeTodos = $query->paginate(10);

        $completedTodos = $this->todoRepository->getUserTodos($user)
            ->where('is_completed', true)
            ->whereNull('deleted_at')
            ->paginate(10);

        $deletedTodos = $this->todoRepository->getUserTodos($user)
            ->onlyTrashed()
            ->paginate(10);

        $sharedTodos = $this->todoRepository->getPrivateTodos($user->getEmail())->paginate(10);

        $categories = $this->categoryRepository->getAllCategories();

        return Inertia::render('Dashboard', [
            'todos' => TodoResource::collection($activeTodos),
            'sharedTodos' => TodoResource::collection($sharedTodos),
            'completedTodos' => TodoResource::collection($completedTodos),
            'deletedTodos' => TodoResource::collection($deletedTodos),
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    public function quickStore(CreateRequest $request)
    {
        Todo::create([
            'name' => $request->getName(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Quick ToDo created successfully!');
    }

    public function update(UpdateRequest $request, $id)
    {
        $todo = $this->todoRepository->findByUserAndId(auth()->id(), $id);
        $this->authorize('update', $todo);
        $this->todoRepository->update($todo, [
            'name' => $request->getName(),
            'description' => $request->getDescription(),
        ]);

        $todo->categories()->sync($request->getCategory());

        $sharedTodo = $this->sharedTodoRepository->findByTodoId($todo->id);

        if ($request->isShared()) {
            if ($request->isPublic()) {
                if (!$sharedTodo) {
                    $sharedTodo = $this->sharedTodoRepository->create([
                        'todo_id' => $todo->id,
                        'share_link' => $request->getSharedLink(),
                        'is_public' => true,
                    ]);
                } else {
                    $this->sharedTodoRepository->update($sharedTodo, [
                        'share_link' => $request->getSharedLink(),
                        'is_public' => true,
                    ]);
                }
                $this->sharedTodoEmailRepository->deleteBySharedTodoId($sharedTodo->id);
            } else {
                if (!$sharedTodo) {
                    $sharedTodo = $this->sharedTodoRepository->create([
                        'todo_id' => $todo->id,
                        'share_link' => $request->getSharedLink(),
                        'is_public' => false,
                    ]);
                } else {
                    $this->sharedTodoRepository->update($sharedTodo, [
                        'is_public' => false,
                    ]);
                }

                $existingEmails = $sharedTodo->emails()->pluck('email')->toArray();
                $newEmails = $request->getSharedEmails();
                $emailsToAdd = array_diff($newEmails, $existingEmails);

                $sharedTodo->emails()->whereNotIn('email', $newEmails)->delete();

                $this->sharedTodoEmailRepository->addEmails($sharedTodo, $emailsToAdd);

                foreach ($emailsToAdd as $email) {
                    Notification::route('mail', $email)
                        ->notify(new TodoSharedNotification($request->getName(), $request->getSharedLink(), auth()->user()));
                }
            }
        } else {
            if ($sharedTodo) {
                $this->sharedTodoRepository->delete($sharedTodo);
            }
        }

        return redirect()->back()->with('status', 'ToDo updated successfully!');
    }

    public function completed(CompletedRequest $request, $id)
    {
        /** @var Todo $todo */
        $todo = auth()->user()->todos()->findOrFail($id);
        $this->authorize('update', $todo);

        $todo->setIsCompleted($request->isCompleted());
        $todo->save();

        /** @var User $user */
        $user = auth()->user();
        Notification::route('mail', $user->getEmail())
            ->notify(new TodoNotification('Todo ' . $todo->getName() . ($request->isCompleted() ? ' is completed' : ' is incompleted again')));

        return redirect()->back()->with('status', 'Todo updated');
    }

    public function destroy($id)
    {
        $todo = $this->todoRepository->findByUserAndId(auth()->id(), $id);
        $this->authorize('delete', $todo);
        $sharedTodo = $this->sharedTodoRepository->findByTodoId($todo->id);

        if ($sharedTodo) {
            $this->sharedTodoEmailRepository->deleteBySharedTodoId($sharedTodo->id);
            $this->sharedTodoRepository->delete($sharedTodo);
        }

        $this->todoRepository->delete($todo);

        /** @var User $user */
        $user = auth()->user();
        Notification::route('mail', $user->getEmail())
            ->notify(new TodoNotification('Todo ' . $todo->getName() . ' was deleted.'));

        return redirect()->route('dashboard')->with('success', 'ToDo deleted successfully!');
    }

    public function restore($id)
    {
        $todo = auth()->user()->todos()->withTrashed()->findOrFail($id);
        $this->authorize('restore', $todo);
        $this->todoRepository->restore($todo);

        /** @var User $user */
        $user = auth()->user();
        Notification::route('mail', $user->getEmail())
            ->notify(new TodoNotification('Todo ' . $todo->getName() . ' was restored.'));

        return redirect()->route('dashboard')->with('success', 'ToDo restored successfully!');
    }
}
