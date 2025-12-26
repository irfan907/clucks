<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'received_by',
        'receiving_date',
        'receiving_note',
        'receiving_sequence',
    ];

    protected function casts(): array
    {
        return [
            'receiving_date' => 'date',
            'receiving_sequence' => 'integer',
        ];
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function receivedItems()
    {
        return $this->hasMany(ReceivedItem::class);
    }
}
