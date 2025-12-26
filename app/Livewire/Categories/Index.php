<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $categories = Category::orderBy('display_order')->orderBy('name')->get();

        return view('livewire.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
