<?php

namespace App\Livewire\Deliveries;

use App\Models\Category;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public Delivery $delivery;
    public ?int $selectedCategoryId = null;
    public string $notes = '';
    public array $items = [];
    public bool $showAllProducts = false;

    public function mount(Delivery $delivery): void
    {
        $this->delivery = $delivery->load('items');
        $this->notes = $delivery->notes ?? '';
        $this->loadItems();
        
        // Initialize to first category
        $firstCategory = Category::where('is_active', true)
            ->orderBy('display_order')
            ->first();
        
        if ($firstCategory) {
            $this->selectedCategoryId = $firstCategory->id;
        }
    }

    protected function loadItems(): void
    {
        $this->items = [];
        foreach ($this->delivery->items as $item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $this->items[$product->category_id][$item->product_id] = [
                        'current_stock' => $item->current_stock ?? '',
                        'quantity' => $item->ordered_quantity,
                        'note' => $item->item_note ?? '',
                    ];
                }
            }
        }
    }

    public function save(): void
    {
        $this->delivery->update(['notes' => $this->notes]);
        
        foreach ($this->items as $categoryId => $products) {
            foreach ($products as $productId => $data) {
                $quantity = $data['quantity'] ?? null;
                
                // Check if quantity is empty, null, or 0 - exclude item in all these cases
                // if ($quantity === null || $quantity === '' || $quantity === '0' || $quantity === 0) {
                //     // Remove item if quantity is not set, empty, or 0
                //     DeliveryItem::where('delivery_id', $this->delivery->id)
                //         ->where('product_id', $productId)
                //         ->delete();
                //     // Also remove from items array
                //     unset($this->items[$categoryId][$productId]);
                //     // Clean up empty category arrays
                //     if (empty($this->items[$categoryId])) {
                //         unset($this->items[$categoryId]);
                //     }
                //     continue;
                // }
                
                // Convert to float/number
                if (!is_numeric($quantity)) {
                    // If not numeric, remove it
                    DeliveryItem::where('delivery_id', $this->delivery->id)
                        ->where('product_id', $productId)
                        ->delete();
                    unset($this->items[$categoryId][$productId]);
                    if (empty($this->items[$categoryId])) {
                        unset($this->items[$categoryId]);
                    }
                    continue;
                }

                $quantityValue = (float) $quantity;
                
                // Only save if quantity > 0
                if ($quantityValue > 0) {
                    $item = DeliveryItem::where('delivery_id', $this->delivery->id)
                        ->where('product_id', $productId)
                        ->first();

                // Get current_stock if provided
                $currentStock = $data['current_stock'] ?? null;
                $currentStockValue = null;
                if ($currentStock !== null && $currentStock !== '' && is_numeric($currentStock)) {
                    $currentStockValue = (float) $currentStock;
                }

                if ($item) {
                    $updateData = [
                        'ordered_quantity' => $quantityValue,
                        'item_note' => $data['note'] ?? '',
                        'last_edited_at' => now(),
                    ];
                    if ($currentStockValue !== null) {
                        $updateData['current_stock'] = $currentStockValue;
                    }
                    $item->update($updateData);
                } else {
                    $product = Product::find($productId);
                    if ($product) {
                        DeliveryItem::create([
                            'delivery_id' => $this->delivery->id,
                            'product_id' => $productId,
                            'current_stock' => $currentStockValue,
                            'product_name_snapshot' => $product->name,
                            'unit_snapshot' => $product->unit,
                            'ordered_quantity' => $quantityValue,
                            'minimum_quantity_snapshot' => $product->minimum_quantity,
                            'item_note' => $data['note'] ?? '',
                            'last_edited_at' => now(),
                        ]);
                    }
                }
                } else {
                    // Quantity is 0 or less, remove item
                    DeliveryItem::where('delivery_id', $this->delivery->id)
                        ->where('product_id', $productId)
                        ->delete();
                    unset($this->items[$categoryId][$productId]);
                    if (empty($this->items[$categoryId])) {
                        unset($this->items[$categoryId]);
                    }
                }
            }
        }
        
        // Refresh delivery items to reflect any deletions
        $this->delivery->refresh();
        $this->delivery->load('items');
        $this->loadItems();
        
        $this->dispatch('delivery-saved');
    }

    public function saveAndGoBack(): void
    {
        $this->save();
        $this->redirect(route('deliveries.show', $this->delivery), navigate: true);
    }

    public function goToNextCategory(): void
    {
        $categories = Category::where('is_active', true)
            ->orderBy('display_order')
            ->get();
        $currentIndex = $categories->search(fn($cat) => $cat->id === $this->selectedCategoryId);
        
        if ($currentIndex !== false) {
            $nextCategory = $categories->get($currentIndex + 1);
            if ($nextCategory) {
                $this->selectedCategoryId = $nextCategory->id;
            }
        }
    }

    public function goToPreviousCategory(): void
    {
        $categories = Category::where('is_active', true)
            ->orderBy('display_order')
            ->get();
        $currentIndex = $categories->search(fn($cat) => $cat->id === $this->selectedCategoryId);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            $previousCategory = $categories->get($currentIndex - 1);
            if ($previousCategory) {
                $this->selectedCategoryId = $previousCategory->id;
            }
        }
    }

    protected function getOrderedCategories()
    {
        // Only get categories that have products in this delivery
        $deliveryProductIds = $this->delivery->items->pluck('product_id')->toArray();
        $deliveryCategoryIds = Product::whereIn('id', $deliveryProductIds)
            ->pluck('category_id')
            ->unique()
            ->toArray();
        
        return Category::where('is_active', true)
        // Only get categories that have products in this delivery unless showAllProducts is true
            ->when(!$this->showAllProducts, function ($query) use ($deliveryCategoryIds) {
                return $query->whereIn('id', $deliveryCategoryIds);
            })
            ->orderBy('display_order')
            ->get();
    }

    public function getCurrentCategoryIndex(): int
    {
        $categories = $this->getOrderedCategories();
        $currentIndex = $categories->search(fn($cat) => $cat->id === $this->selectedCategoryId);
        return $currentIndex !== false ? $currentIndex + 1 : 0;
    }

    public function getTotalCategoriesCount(): int
    {
        return $this->getOrderedCategories()->count();
    }

    public function isFirstCategory(): bool
    {
        return $this->getCurrentCategoryIndex() === 1;
    }

    public function isLastCategory(): bool
    {
        return $this->getCurrentCategoryIndex() === $this->getTotalCategoriesCount();
    }

    public function render()
    {
        $currentCategory = null;
        $categories = $this->getOrderedCategories();
        
        // Prepare products for current category only
        $productsForTable = [];
        if ($this->selectedCategoryId) {
            $currentCategory = Category::with(['activeProducts'])
                ->where('id', $this->selectedCategoryId)
                ->where('is_active', true)
                ->first();
            
            if ($currentCategory) {
                // For submitted deliveries, only show products that are already in the delivery
                $deliveryProductIds = $this->delivery->items->pluck('product_id')->toArray();
                
                foreach ($currentCategory->activeProducts as $product) {
                    // Only include products that are already in the delivery unless showAllProducts is true
                    if (!$this->showAllProducts && !in_array($product->id, $deliveryProductIds)) {
                        continue;
                    }
                    
                    $categoryId = $currentCategory->id;
                    $productId = $product->id;
                    $productsForTable[] = [
                        'category' => $currentCategory,
                        'product' => $product,
                        'category_id' => $categoryId,
                        'product_id' => $productId,
                        'current_stock' => $this->items[$categoryId][$productId]['current_stock'] ?? '',
                        'quantity' => $this->items[$categoryId][$productId]['quantity'] ?? '',
                        'note' => $this->items[$categoryId][$productId]['note'] ?? '',
                    ];
                }
            }
        }

        $currentIndex = $categories->search(fn($cat) => $cat->id === $this->selectedCategoryId);
        $currentCategoryIndex = $currentIndex !== false ? $currentIndex + 1 : 0;
        $totalCategoriesCount = $categories->count();
        $isFirstCategory = $currentCategoryIndex === 1;
        $isLastCategory = $currentCategoryIndex === $totalCategoriesCount;

        return view('livewire.deliveries.edit', [
            'currentCategory' => $currentCategory,
            'productsForTable' => $productsForTable,
            'currentCategoryIndex' => $currentCategoryIndex,
            'totalCategoriesCount' => $totalCategoriesCount,
            'isFirstCategory' => $isFirstCategory,
            'isLastCategory' => $isLastCategory,
        ]);
    }
}
