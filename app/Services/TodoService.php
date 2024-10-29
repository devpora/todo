<?php

namespace App\Services;

use App\Http\Requests\Todo\CompletedRequest;
use App\Http\Requests\Todo\QueryRequest;
use App\Http\Requests\Todo\UpdateRequest;
use App\Models\Todo;
use App\Models\User;
use App\Notifications\TodoEditedNotification;
use App\Notifications\TodoNotification;
use App\Notifications\TodoSharedNotification;
use App\Repositories\CategoryRepository;
use App\Repositories\SharedTodoEmailRepository;
use App\Repositories\SharedTodoRepository;
use App\Repositories\TodoRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Notification;

class TodoService{

    public function __construct(
        private TodoRepository $todoRepository,
        private SharedTodoRepository $sharedTodoRepository,
        private SharedTodoEmailRepository $sharedTodoEmailRepository,
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

    public function createTodo(string $name, int $userId): Todo
    {
        return $this->todoRepository->create([
            'name' => $name,
            'user_id' => $userId,
        ]);
    }

    public function updateTodoWithSharing(Todo $todo, UpdateRequest $request): void
    {
        $this->updateTodo($todo, $request);
        $this->handleSharingLogic($todo, $request);
    }

    public function setAsCompleted(Todo $todo, CompletedRequest $request): void
    {
        $todo->setIsCompleted($request->isCompleted());
        $todo->save();

        /** @var User $user */
        $user = auth()->user();
        $this->sendNotification(
            $todo->getName(),
            $user->getEmail(),
            $request->isCompleted() ? 'is completed' : 'is incompleted again'
        );
    }

    public function destroy(Todo $todo): void
    {
        $sharedTodo = $this->sharedTodoRepository->findByTodoId($todo->getId());

        if ($sharedTodo) {
            $this->sharedTodoEmailRepository->deleteBySharedTodoId($sharedTodo->getId());
            $this->sharedTodoRepository->delete($sharedTodo);
        }

        $this->todoRepository->delete($todo);

        /** @var User $user */
        $user = auth()->user();
        $this->sendNotification(
            $todo->getName(),
            $user->getEmail(),
            'was deleted'
        );
    }

    public function forceDestroy(Todo $todo): void
    {
        $sharedTodo = $this->sharedTodoRepository->findByTodoId($todo->getId());

        if ($sharedTodo) {
            $this->sharedTodoEmailRepository->deleteBySharedTodoId($sharedTodo->getId());
            $this->sharedTodoRepository->delete($sharedTodo);
        }

        $this->todoRepository->forceDelete($todo);

        /** @var User $user */
        $user = auth()->user();
        $this->sendNotification(
            $todo->getName(),
            $user->getEmail(),
            'has been permanently deleted'
        );
    }

    public function restore(Todo $todo): void
    {
        $this->todoRepository->restore($todo);

        /** @var User $user */
        $user = auth()->user();
        $this->sendNotification(
            $todo->getName(),
            $user->getEmail(),
            'has been restored'
        );
    }

    private function updateTodo(Todo $todo, UpdateRequest $request): void
    {
        $this->todoRepository->update($todo, [
            'name' => $request->getName(),
            'description' => $request->getDescription(),
        ]);

        $todo->categories()->sync($request->getCategory());
    }


    private function handleSharingLogic(Todo $todo, UpdateRequest $request): void
    {
        $sharedTodo = $this->sharedTodoRepository->findByTodoId($todo->getId());

        if (!$request->isShared()) {
            if ($sharedTodo) {
                $this->sharedTodoRepository->delete($sharedTodo);
            }
            return;
        }

        $this->handleSharing($sharedTodo, $todo, $request);
    }

    private function handleSharing($sharedTodo, Todo $todo, UpdateRequest $request): void
    {
        if ($request->isPublic()) {
            $this->handlePublicSharing($sharedTodo, $todo, $request);
        } else {
            $this->handlePrivateSharing($sharedTodo, $todo, $request);
        }
    }

    private function handlePublicSharing($sharedTodo, Todo $todo, UpdateRequest $request): void
    {
        if (!$sharedTodo) {
            $sharedTodo = $this->sharedTodoRepository->create([
                'todo_id' => $todo->getId(),
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
    }

    private function handlePrivateSharing($sharedTodo, Todo $todo, UpdateRequest $request): void
    {
        if (!$sharedTodo) {
            $sharedTodo = $this->sharedTodoRepository->create([
                'todo_id' => $todo->getId(),
                'share_link' => $request->getSharedLink(),
                'is_public' => false,
            ]);
        } else {
            $this->sharedTodoRepository->update($sharedTodo, [
                'is_public' => false,
            ]);
        }

        $this->synchronizeEmailsAndNotify($sharedTodo, $request);
    }

    private function synchronizeEmailsAndNotify($sharedTodo, UpdateRequest $request): void
    {
        $existingEmails = $sharedTodo->emails()->pluck('email')->toArray();
        $newEmails = $request->getSharedEmails();
        $notificationShared = array_diff($newEmails, $existingEmails);
        $notificationEdit = array_intersect($newEmails, $existingEmails);

        $sharedTodo->emails()->whereNotIn('email', $newEmails)->delete();
        $this->sharedTodoEmailRepository->addEmails($sharedTodo, $notificationShared);

        $this->sendNotifications($request->getName(), $request->getSharedLink(), $notificationShared, $notificationEdit);
    }

    private function sendNotification(string $todoName, string $userEmail, string $msg): void
    {
        Notification::route('mail', $userEmail)
            ->notify(new TodoNotification(
                "Todo $todoName $msg."
            ));
    }

    private function sendNotifications(string $todoName, string $sharedLink, array $notificationShared, array $notificationEdit): void
    {
        $currentUser = auth()->user();

        foreach ($notificationShared as $email) {
            Notification::route('mail', $email)
                ->notify(new TodoSharedNotification($todoName, $sharedLink, $currentUser));
        }

        foreach ($notificationEdit as $email) {
            Notification::route('mail', $email)
                ->notify(new TodoEditedNotification($todoName, $sharedLink, $currentUser));
        }
    }

}
