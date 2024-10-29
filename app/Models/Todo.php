<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'todos';

    protected $fillable = ['name', 'description', 'category_id', 'user_id', 'is_completed'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    public function getDeletedAt(): ?Carbon
    {
        return $this->deleted_at;
    }

    public function setName(string $value): void
    {
        $this->name = $value;
    }

    public function setDescription(?string $value): void
    {
        $this->description = $value;
    }

    public function setIsCompleted(bool $value): void
    {
        $this->is_completed = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_todo');
    }

    public function sharedTodo(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SharedTodo::class);
    }
}
