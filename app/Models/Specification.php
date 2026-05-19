<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specification extends Model
{
    protected $fillable = [
        'name',
        'key',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }
}