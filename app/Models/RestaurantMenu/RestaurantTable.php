<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    protected $table = 'restaurant_tables';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'name',
        'number',
        'code',
        'seats',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'seats' => 'integer',
        'is_active' => 'boolean',
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

    public function orders(): HasMany
    {
        return $this->hasMany(RestaurantOrder::class, 'table_id');
    }

  public function publicMenuUrl(): string
{
    return route('public.restaurant-menu.branch', [
        'workspace' => $this->workspace,
        'branch' => $this->branch,
    ]) . '?table_code=' . urlencode($this->code);
}









public function invoices(): HasMany
{
    return $this->hasMany(RestaurantInvoice::class, 'table_id');
}

public function openInvoices(): HasMany
{
    return $this->hasMany(RestaurantInvoice::class, 'table_id')
        ->where('status', 'open')
        ->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
}
}