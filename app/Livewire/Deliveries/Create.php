<?php

namespace App\Livewire\Deliveries;

use App\DeliveryStatus;
use App\Models\Category;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Product;
use App\Services\DeliveryService;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public ?Delivery $delivery = null;
    public ?int $selectedCategoryId = null;
    public string $notes = '';
    public array $items = []; // [category_id => [product_id => ['current_stock' => float, 'quantity' => float, 'note' => string]]]

    public function mount(): void
    {
        $deliveryService = new DeliveryService();
        $this->delivery = $deliveryService->getOrCreateDraft(Auth::user());
        $this->notes = $this->delivery->notes ?? '';
        
        // Load existing items
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
        // Load existing items from delivery, including current_stock
        $this->items = [];
        foreach ($this->delivery->items as $item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $this->items[$product->category_id][$item->product_id] = [
                        'current_stock' => $item->current_stock ?? '', // Load current_stock from delivery item
                        'quantity' => '', // Don't pre-fill quantity (it's calculated)
                        'note' => $item->item_note ?? '',
                    ];
                }
            }
        }
    }

    public function saveItems(): void
    {
        $this->delivery->update(['notes' => $this->notes]);
        $stockService = new StockService();
        
        // Save/update items based on current_stock input
        foreach ($this->items as $categoryId => $products) {
            foreach ($products as $productId => $data) {
                $currentStock = $data['current_stock'] ?? null;
                
                // If current_stock is empty/null, ignore this item
                if ($currentStock === null || $currentStock === '') {
                    // Remove item if current_stock is not set (empty/null)
                    DeliveryItem::where('delivery_id', $this->delivery->id)
                        ->where('product_id', $productId)
                        ->delete();
                    // Also remove from items array
                    unset($this->items[$categoryId][$productId]);
                    // Clean up empty category arrays
                    if (empty($this->items[$categoryId])) {
                        unset($this->items[$categoryId]);
                    }
                    continue;
                }
                
                // Convert to number
                if (!is_numeric($currentStock)) {
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

                // Current stock has a value (including 0)
                $currentStockValue = (float) $currentStock;
                
                $product = Product::find($productId);
                if (!$product) continue;

                // Check if current stock is below minimum quantity
                $isBelowMinimum = $product->minimum_quantity && $currentStockValue < $product->minimum_quantity;
                
                // If stock is below minimum, use default_ordered_quantity if available
                $orderedQuantity = 0;
                if ($isBelowMinimum && $product->default_ordered_quantity) {
                    $orderedQuantity = $product->default_ordered_quantity;
                }

                // Add item to delivery with current_stock and calculated ordered_quantity
                DeliveryItem::updateOrCreate(
                    [
                        'delivery_id' => $this->delivery->id,
                        'product_id' => $productId,
                    ],
                    [
                        'current_stock' => $currentStockValue,
                        'product_name_snapshot' => $product->name,
                        'unit_snapshot' => $product->unit,
                        'ordered_quantity' => $orderedQuantity,
                        'minimum_quantity_snapshot' => $product->minimum_quantity,
                        'item_note' => $data['note'] ?? '',
                        'last_edited_at' => now(),
                    ]
                );
            }
        }
    }

    public function save(): void
    {
        $this->saveItems();
        $this->dispatch('delivery-saved');
    }

    public function saveAndNext(): void
    {
        $this->saveItems();
        $this->goToNextCategory();
        $this->dispatch('delivery-saved');
    }

    public function goToNextCategory(): void
    {
        $categories = $this->getOrderedCategories();
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
        $categories = $this->getOrderedCategories();
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
        return Category::where('is_active', true)
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

    public function submit(): void
    {
        // Reload all items from delivery before save to ensure we have complete data
        $this->delivery->refresh();
        $this->delivery->load('items');
        $this->loadItems();
        
        // Save all current changes
        $this->saveItems();
        
        // Refresh delivery again to get latest items after autosave
        $this->delivery->refresh();
        $this->delivery->load('items');
        
        if ($this->delivery->items->isEmpty()) {
            $this->addError('items', __('Delivery must have at least one item. Right now it has none below the minimum quantity in this delivery.'));
            return;
        }
        
        $deliveryService = new DeliveryService();
        $deliveryService->submitDelivery($this->delivery);
        
        $this->redirect(route('deliveries.my'), navigate: true);
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
                foreach ($currentCategory->activeProducts as $product) {
                    $categoryId = $currentCategory->id;
                    $productId = $product->id;
                    
                    // For draft deliveries, only show current_stock from items array (user input)
                    // Don't auto-fill from existing delivery items on refresh
                    $currentStock = $this->items[$categoryId][$productId]['current_stock'] ?? '';
                    
                    $productsForTable[] = [
                        'category' => $currentCategory,
                        'product' => $product,
                        'category_id' => $categoryId,
                        'product_id' => $productId,
                        'current_stock' => $currentStock,
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

        return view('livewire.deliveries.create', [
            'currentCategory' => $currentCategory,
            'productsForTable' => $productsForTable,
            'currentCategoryIndex' => $currentCategoryIndex,
            'totalCategoriesCount' => $totalCategoriesCount,
            'isFirstCategory' => $isFirstCategory,
            'isLastCategory' => $isLastCategory,
        ]);
    }
}
