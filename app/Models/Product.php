<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'location_id',
        'brand',
        'item_type',
        'unit',
        'purchase_price',
        'selling_price',
        'stock',
        'minimum_stock',
        'description',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function stockInItems()
    {
        return $this->hasMany(StockInItem::class);
    }

    public function stockOutItems()
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }
}