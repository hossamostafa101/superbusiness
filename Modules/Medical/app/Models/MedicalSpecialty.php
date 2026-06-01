<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalSpecialty extends Model
{
    protected $table = 'medical_specialties';

    protected $fillable = [
        'workspace_id',
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(MedicalStaff::class, 'specialty_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(MedicalService::class, 'specialty_id');
    }
}