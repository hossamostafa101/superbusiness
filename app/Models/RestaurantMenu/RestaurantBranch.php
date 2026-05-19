<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantBranch extends Model
{
    protected $table = 'restaurant_branches';

    protected $fillable = [
        'workspace_id',
        'name',
        'slug',
        'phone',
        'whatsapp_number',
        'address',
        'location_url',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(RestaurantMenuCategory::class, 'branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RestaurantMenuItem::class, 'branch_id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(RestaurantMenuSetting::class, 'branch_id');
    }












    public function itemVariants(): HasMany
{
    return $this->hasMany(RestaurantItemVariant::class, 'branch_id');
}

public function itemOptionGroups(): HasMany
{
    return $this->hasMany(RestaurantItemOptionGroup::class, 'branch_id');
}

public function itemOptions(): HasMany
{
    return $this->hasMany(RestaurantItemOption::class, 'branch_id');
}





public function orders(): HasMany
{
    return $this->hasMany(RestaurantOrder::class, 'branch_id');
}

public function orderItems(): HasMany
{
    return $this->hasMany(RestaurantOrderItem::class, 'branch_id');
}

public function orderItemOptions(): HasMany
{
    return $this->hasMany(RestaurantOrderItemOption::class, 'branch_id');
}






public function tables(): HasMany
{
    return $this->hasMany(RestaurantTable::class, 'branch_id');
}

public function activeTables(): HasMany
{
    return $this->hasMany(RestaurantTable::class, 'branch_id')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id');
}






public function invoices(): HasMany
{
    return $this->hasMany(RestaurantInvoice::class, 'branch_id');
}

public function openInvoices(): HasMany
{
    return $this->hasMany(RestaurantInvoice::class, 'branch_id')
        ->where('status', 'open')
        ->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
}




public function contentSections()
{
    return $this->hasMany(RestaurantMenuContentSection::class, 'branch_id');
}

public function offers()
{
    return $this->hasMany(RestaurantMenuOffer::class, 'branch_id');
}
}