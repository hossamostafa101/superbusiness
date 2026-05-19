<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessLead extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'phone',
        'email',
        'message',
        'source',
        'business_product_id',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function businessProduct(): BelongsTo
    {
        return $this->belongsTo(BusinessProduct::class);
    }
}