<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProduct extends Model
{
    protected $fillable = [
        'workspace_id',
        'category_id',
        'name',
        'description',
        'price',
        'sale_price',
        'currency',
        'image',
        'sort_order',
        'is_available',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BusinessCategory::class, 'category_id');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function finalPrice(): ?string
    {
        if ($this->sale_price !== null) {
            return $this->sale_price;
        }

        return $this->price;
    }
}