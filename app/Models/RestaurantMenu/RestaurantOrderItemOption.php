<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantOrderItemOption extends Model
{
    protected $table = 'restaurant_order_item_options';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'order_id',
        'order_item_id',

        'option_group_id',
        'option_id',

        'group_name',
        'option_name',

        'price',
        'currency',
    ];

    protected $casts = [
        'price' => 'decimal:2',
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

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(RestaurantOrderItem::class, 'order_item_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(RestaurantItemOptionGroup::class, 'option_group_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(RestaurantItemOption::class, 'option_id');
    }
}