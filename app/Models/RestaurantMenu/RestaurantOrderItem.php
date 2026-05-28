<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantOrderItem extends Model
{
    protected $table = 'restaurant_order_items';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'order_id',

        'item_id',
        'variant_id',

        'item_name',
        'variant_name',

        'quantity',

        'unit_price',
        'options_total',
        'line_total',

        'currency',

        'notes',
        'metadata',

        'line_type',
'offer_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'options_total' => 'decimal:2',
        'line_total' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrder::class, 'order_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'item_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(RestaurantItemVariant::class, 'variant_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(RestaurantOrderItemOption::class, 'order_item_id');
    }


    public function offer(): BelongsTo
{
    return $this->belongsTo(RestaurantMenuOffer::class, 'offer_id');
}
}