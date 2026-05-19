<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantItemOption extends Model
{
    protected $table = 'restaurant_item_options';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'item_id',
        'option_group_id',
        'name',
        'price',
        'currency',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
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

    public function group(): BelongsTo
    {
        return $this->belongsTo(RestaurantItemOptionGroup::class, 'option_group_id');
    }






    public function orderItemOptions(): HasMany
{
    return $this->hasMany(RestaurantOrderItemOption::class, 'option_id');
}
}