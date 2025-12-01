<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    // Relationship: Tag belongs to many Products (many-to-many)
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
