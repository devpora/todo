<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function getAllCategories()
    {
        return Category::all();
    }
    public function getIdsByName($names)
    {
        return Category::whereIn('name', $names)->pluck('id');
    }
}

