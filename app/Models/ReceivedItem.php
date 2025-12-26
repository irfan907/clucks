<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiving_id',
        'delivery_item_id',
        'received_quantity',
        'item_note',
        'recorded_date',
    ];

    protected function casts(): array
    {
        return [
            'received_quantity' => 'decimal:2',
            'recorded_date' => 'date',
        ];
    }

    public function receiving()
    {
        return $this->belongsTo(Receiving::class);
    }

    public function deliveryItem()
    {
        return $this->belongsTo(DeliveryItem::class);
    }
}
