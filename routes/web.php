<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharedTodoController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;


Route::get('/shared/public/{slug}', [SharedTodoController::class, 'showPublic'])->name('shared.public');

Route::middleware('auth')->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('dashboard');
    Route::post('/todo/quickStore', [TodoController::class, 'quickStore'])->name('todo.quickStore');
    Route::post('/todo/update/{id}', [TodoController::class, 'update'])->name('todo.update');
    Route::post('/todo-completed/{id}', [TodoController::class, 'completed']);
    Route::post('/todo-restore/{id}', [TodoController::class, 'restore'])->name('todo.restore');
    Route::delete('/todo/{id}', [TodoController::class, 'destroy'])->name('todo.destroy');

    Route::get('/shared/private/{slug}', [SharedTodoController::class, 'showPrivate'])->name('shared.private');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';