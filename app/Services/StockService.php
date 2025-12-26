<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ReceivedItem;
use Illuminate\Support\Collection;

class StockService
{
    public function getCurrentStock(Product $product): float
    {
        return ReceivedItem::whereHas('deliveryItem', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->sum('received_quantity');
    }

    public function getCurrentStockForAllProducts(): Collection
    {
        $products = Product::with('category')->get();
        
        return $products->map(function (Product $product) {
            return [
                'product' => $product,
                'current_stock' => $this->getCurrentStock($product),
                'is_low_stock' => $product->minimum_quantity 
                    ? $this->getCurrentStock($product) < $product->minimum_quantity 
                    : false,
            ];
        });
    }

    public function getStockByCategory(): Collection
    {
        $stockData = $this->getCurrentStockForAllProducts();
        
        return $stockData->groupBy(function ($item) {
            return $item['product']->category_id;
        })->map(function ($items, $categoryId) {
            $category = $items->first()['product']->category;
            return [
                'category' => $category,
                'products' => $items->map(function ($item) {
                    return [
                        'product' => $item['product'],
                        'current_stock' => $item['current_stock'],
                        'is_low_stock' => $item['is_low_stock'],
                    ];
                }),
            ];
        })->sortBy(function ($group) {
            return $group['category']->display_order;
        });
    }
}

