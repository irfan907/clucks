<?php

namespace App\Models;

use App\DeliveryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'created_by',
        'status',
        'notes',
        'submitted_at',
        'approved_at',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => DeliveryStatus::class,
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'finalized_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function receivings()
    {
        return $this->hasMany(Receiving::class)->orderBy('receiving_sequence');
    }

    public function isDraft(): bool
    {
        return $this->status === DeliveryStatus::Draft;
    }

    public function isSubmitted(): bool
    {
        return $this->status === DeliveryStatus::Submitted;
    }

    public function isApproved(): bool
    {
        return $this->status === DeliveryStatus::Approved;
    }

    public function isDelivered(): bool
    {
        return $this->status === DeliveryStatus::Delivered;
    }
}
