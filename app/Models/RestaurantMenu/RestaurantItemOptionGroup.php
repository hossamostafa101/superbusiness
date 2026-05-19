<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantItemOptionGroup extends Model
{
    protected $table = 'restaurant_item_option_groups';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'item_id',
        'name',
        'type',
        'is_required',
        'min_choices',
        'max_choices',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
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

    public function options(): HasMany
    {
        return $this->hasMany(RestaurantItemOption::class, 'option_group_id');
    }







    public function orderItemOptions(): HasMany
{
    return $this->hasMany(RestaurantOrderItemOption::class, 'option_group_id');
}
}