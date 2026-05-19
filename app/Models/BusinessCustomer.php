<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessCustomer extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'phone',
        'email',
        'gender',
        'birthdate',
        'source',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'metadata' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(BusinessAppointment::class, 'customer_id');
    }
}