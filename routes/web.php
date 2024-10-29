<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharedTodoController;
use App\Http\Controllers\TodoController;
use App\Models\Todo;
use Illuminate\Support\Facades\Route;

Route::get('/shared/public/{slug}', [SharedTodoController::class, 'showPublic'])->name('shared.public');

Route::middleware('auth')->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('dashboard');

    Route::get('/todo-active', [DashboardController::class, 'getActive']);
    Route::get('/todo-shared', [DashboardController::class, 'getShared']);
    Route::get('/todo-completed', [DashboardController::class, 'getCompleted']);
    Route::get('/todo-deleted', [DashboardController::class, 'getDeleted']);

    Route::post('/todo/quickStore', [TodoController::class, 'quickStore'])->name('todo.quickStore');
    Route::post('/todo/update/{todo}', [TodoController::class, 'update'])->name('todo.update');
    Route::post('/todo-completed/{todo}', [TodoController::class, 'completed']);
    Route::delete('/todo/{todo}', [TodoController::class, 'destroy']);

    Route::get('/shared/private/{slug}', [SharedTodoController::class, 'showPrivate'])->name('shared.private');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::bind('todoWithTrashed', function ($value) {
        return Todo::withTrashed()->findOrFail($value);
    });
    Route::post('/todo-restore/{todoWithTrashed}', [TodoController::class, 'restore'])->name('todo.restore');
    Route::delete('/todo-force/{todoWithTrashed}', [TodoController::class, 'forceDestroy'])->name('todo.forceDestroy');
});

require __DIR__.'/auth.php';
