<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantInvoiceGuest extends Model
{
    protected $table = 'restaurant_invoice_guests';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'invoice_id',
        'guest_token',
        'customer_name',
        'customer_phone',
        'device_label',
        'is_owner',
        'joined_at',
        'last_seen_at',
    ];

    protected $casts = [
        'is_owner' => 'boolean',
        'joined_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(RestaurantInvoice::class, 'invoice_id');
    }
}