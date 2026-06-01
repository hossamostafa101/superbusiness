<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalBranch extends Model
{
    protected $table = 'medical_branches';

    protected $fillable = [
        'workspace_id',
        'name',
        'slug',
        'phone',
        'whatsapp_number',
        'email',
        'address',
        'city',
        'area',
        'google_maps_url',
        'is_main',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(MedicalDepartment::class, 'branch_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(MedicalService::class, 'branch_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(MedicalStaff::class, 'branch_id');
    }




    public function staffWorkingHours(): HasMany
{
    return $this->hasMany(MedicalStaffWorkingHour::class, 'branch_id');
}
}