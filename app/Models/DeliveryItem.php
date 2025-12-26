<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'product_id',
        'current_stock',
        'product_name_snapshot',
        'unit_snapshot',
        'ordered_quantity',
        'minimum_quantity_snapshot',
        'item_note',
        'last_edited_at',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'ordered_quantity' => 'decimal:2',
            'minimum_quantity_snapshot' => 'decimal:2',
            'last_edited_at' => 'datetime',
        ];
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function receivedItems()
    {
        return $this->hasMany(ReceivedItem::class);
    }

    public function getTotalReceivedAttribute(): float
    {
        return $this->receivedItems()->sum('received_quantity');
    }

    public function getRemainingQuantityAttribute(): float
    {
        return max(0, $this->ordered_quantity - $this->total_received);
    }
}
