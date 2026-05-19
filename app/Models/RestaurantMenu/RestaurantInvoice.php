<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantInvoice extends Model
{
    protected $table = 'restaurant_invoices';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'table_id',
        'invoice_number',
        'table_number',
        'opened_by_name',
        'opened_by_phone',
        'pin_hash',
        'pin_hint',
        'mode',
        'status',
        'subtotal',
        'discount_total',
        'delivery_fee',
        'tax_total',
        'total',
        'currency',
        'opened_at',
        'expires_at',
        'closed_at',
        'last_activity_at',
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'opened_at' => 'datetime',
        'expires_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime',
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

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(RestaurantInvoiceGuest::class, 'invoice_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RestaurantInvoiceItem::class, 'invoice_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(RestaurantOrder::class, 'invoice_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThanOrEqualTo($this->expires_at);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'مفتوحة',
            'closed' => 'مغلقة',
            'expired' => 'منتهية',
            'cancelled' => 'ملغية',
            default => $this->status,
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'bg-success',
            'closed' => 'bg-dark',
            'expired' => 'bg-warning text-dark',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}