<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantItemVariant extends Model
{
    protected $table = 'restaurant_item_variants';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'item_id',
        'name',
        'price',
        'sale_price',
        'currency',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'item_id');
    }

    public function finalPrice(): float
    {
        return (float) ($this->sale_price ?? $this->price ?? 0);
    }






    public function orderItems(): HasMany
{
    return $this->hasMany(RestaurantOrderItem::class, 'variant_id');
}
}