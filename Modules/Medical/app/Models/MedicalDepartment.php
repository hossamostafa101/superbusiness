<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalDepartment extends Model
{
    protected $table = 'medical_departments';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(MedicalBranch::class, 'branch_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(MedicalService::class, 'department_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(MedicalStaff::class, 'department_id');
    }
}