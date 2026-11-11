<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $fillable = [
        'transaction_code',
        'transaction_date',
        'source',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockInItem::class);
    }
}