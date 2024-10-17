<?php

namespace App\Models;

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
    public function getCategoryId(): int
    {
        return $this->category_id;
    }
    public function getUserId(): int
    {
        return $this->user_id;
    }
    public function isCompleted(): bool
    {
        return $this->is_completed;
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

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_todo');
    }

    public function sharedTodo()
    {
        return $this->hasOne(SharedTodo::class);
    }

}

