<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BusinessRequest extends Model
{
    protected $fillable = [
        'workspace_id',
        'type',
        'source',
        'reference_type',
        'reference_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'title',
        'message',
        'status',
        'priority',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo(
            name: 'reference',
            type: 'reference_type',
            id: 'reference_id'
        );
    }
}