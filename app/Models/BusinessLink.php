<?php

namespace App\Models;

use App\Support\WorkspaceTranslatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessLink extends Model
{
    use WorkspaceTranslatable;
    
    protected $fillable = [
        'workspace_id',
        'title',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}