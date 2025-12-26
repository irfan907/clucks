<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'display_order',
        'colour',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('display_order');
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true)->orderBy('display_order');
    }
}
