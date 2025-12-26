<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Services\StockService;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $categories = Category::with(['activeProducts'])->orderBy('display_order')->get();
        $stockService = new StockService();

        // Prepare all products for table view
        $productsForTable = [];
        foreach ($categories as $category) {
            foreach ($category->activeProducts as $product) {
                $currentStock = $stockService->getCurrentStock($product);
                $isLowStock = $product->minimum_quantity && $currentStock < $product->minimum_quantity;
                $productsForTable[] = [
                    'category' => $category,
                    'product' => $product,
                    'current_stock' => $currentStock,
                    'is_low_stock' => $isLowStock,
                ];
            }
        }

        return view('livewire.products.index', [
            'categories' => $categories,
            'stockService' => $stockService,
            'productsForTable' => $productsForTable,
        ]);
    }

    public function delete($productId): void
    {
        $product = \App\Models\Product::findOrFail($productId);
        $product->delete();
    }
}
