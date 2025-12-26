<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Product $product;
    public int $category_id = 0;
    public string $name = '';
    public string $unit = '';
    public ?float $minimum_quantity = null;
    public ?float $default_ordered_quantity = null;
    public int $display_order = 0;
    public bool $is_active = true;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->unit = $product->unit;
        $this->minimum_quantity = $product->minimum_quantity;
        $this->default_ordered_quantity = $product->default_ordered_quantity;
        $this->display_order = $product->display_order;
        $this->is_active = $product->is_active;
    }

    protected function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->where('category_id', $this->category_id)->ignore($this->product->id)],
            'unit' => ['required', 'string', 'max:50'],
            'minimum_quantity' => ['nullable', 'numeric', 'min:0'],
            'default_ordered_quantity' => ['nullable', 'numeric', 'min:0'],
            'display_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $this->product->update($validated);

        $this->redirect(route('products.index'), navigate: true);
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();

        return view('livewire.products.edit', [
            'categories' => $categories,
        ]);
    }
}
