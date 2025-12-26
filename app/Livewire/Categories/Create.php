<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';
    public ?string $description = null;
    public int $display_order = 0;
    public string $colour = '#3B82F6';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'display_order' => ['required', 'integer', 'min:0'],
            'colour' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        Category::create($validated);

        $this->redirect(route('categories.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.categories.create');
    }
}
