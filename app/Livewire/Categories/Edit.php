<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Category $category;
    public string $name = '';
    public ?string $description = null;
    public int $display_order = 0;
    public string $colour = '#3B82F6';
    public bool $is_active = true;

    public function mount(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->display_order = $category->display_order;
        $this->colour = $category->colour;
        $this->is_active = $category->is_active;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($this->category->id)],
            'description' => ['nullable', 'string'],
            'display_order' => ['required', 'integer', 'min:0'],
            'colour' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $this->category->update($validated);

        $this->redirect(route('categories.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.categories.edit');
    }
}
