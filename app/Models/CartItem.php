<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relationship: Cart item belongs to a User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Cart item belongs to a Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
