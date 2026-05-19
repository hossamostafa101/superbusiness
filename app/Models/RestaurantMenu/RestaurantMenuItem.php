<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantMenuItem extends Model
{
    protected $table = 'restaurant_menu_items';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'category_id',
        'name',
        'description',
        'price',
        'sale_price',
        'currency',
        'image',
        'calories',
        'preparation_time_minutes',
        'is_available',
        'is_featured',
        'sort_order',
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuCategory::class, 'category_id');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function finalPrice(): float
    {
        return (float) ($this->sale_price ?? $this->price ?? 0);
    }









    public function variants(): HasMany
{
    return $this->hasMany(RestaurantItemVariant::class, 'item_id');
}

public function activeVariants(): HasMany
{
    return $this->hasMany(RestaurantItemVariant::class, 'item_id')
        ->where('is_active', true)
        ->orderByDesc('is_default')
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function optionGroups(): HasMany
{
    return $this->hasMany(RestaurantItemOptionGroup::class, 'item_id');
}

public function activeOptionGroups(): HasMany
{
    return $this->hasMany(RestaurantItemOptionGroup::class, 'item_id')
        ->where('is_active', true)
        ->with(['options' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id');
        }])
        ->orderBy('sort_order')
        ->orderBy('id');
}











public function orderItems(): HasMany
{
    return $this->hasMany(RestaurantOrderItem::class, 'item_id');
}
}