<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessEvent extends Model
{
    protected $fillable = [
        'workspace_id',
        'event_type',
        'business_link_id',
        'business_product_id',
        'visitor_id',
        'ip_address',
        'user_agent',
        'referer',
        'target_url',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function businessLink(): BelongsTo
    {
        return $this->belongsTo(BusinessLink::class);
    }

    public function businessProduct(): BelongsTo
    {
        return $this->belongsTo(BusinessProduct::class);
    }
}