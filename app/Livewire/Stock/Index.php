<?php

namespace App\Livewire\Stock;

use App\Services\StockService;
use Livewire\Component;

class Index extends Component
{
    public ?int $categoryFilter = null;

    public function render()
    {
        $stockService = new StockService();
        $stockByCategory = $stockService->getStockByCategory();

        if ($this->categoryFilter) {
            $stockByCategory = $stockByCategory->filter(function ($group) {
                return $group['category']->id === $this->categoryFilter;
            });
        }

        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Prepare all products for table view
        $productsForTable = [];
        foreach ($stockByCategory as $group) {
            foreach ($group['products'] as $item) {
                $productsForTable[] = [
                    'category' => $group['category'],
                    'product' => $item['product'],
                    'current_stock' => $item['current_stock'],
                    'is_low_stock' => $item['is_low_stock'],
                ];
            }
        }

        return view('livewire.stock.index', [
            'stockByCategory' => $stockByCategory,
            'categories' => $categories,
            'productsForTable' => $productsForTable,
        ]);
    }

    public function clearFilter(): void
    {
        $this->categoryFilter = null;
    }
}
