<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessService extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'currency',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(BusinessAppointment::class, 'service_id');
    }
}