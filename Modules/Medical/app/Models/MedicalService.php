<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalService extends Model
{
    protected $table = 'medical_services';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'department_id',
        'specialty_id',
        'type',
        'name',
        'slug',
        'description',
        'duration_minutes',
        'price',
        'currency',
        'requires_doctor',
        'requires_appointment',
        'requires_sample',
        'requires_report',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'requires_doctor' => 'boolean',
        'requires_appointment' => 'boolean',
        'requires_sample' => 'boolean',
        'requires_report' => 'boolean',
        'is_featured' => 'boolean',
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

    public function department(): BelongsTo
    {
        return $this->belongsTo(MedicalDepartment::class, 'department_id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(MedicalSpecialty::class, 'specialty_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'consultation' => 'كشف',
            'follow_up' => 'متابعة',
            'procedure' => 'إجراء طبي',
            'lab_test' => 'تحليل',
            'scan' => 'أشعة',
            'operation' => 'عملية',
            'session' => 'جلسة',
            'package' => 'باقة',
            default => 'أخرى',
        };
    }


    public function staffServices(): HasMany
{
    return $this->hasMany(MedicalStaffService::class, 'service_id');
}

public function staff(): BelongsToMany
{
    return $this->belongsToMany(
        MedicalStaff::class,
        'medical_staff_services',
        'service_id',
        'staff_id'
    )
        ->withPivot([
            'price_override',
            'duration_override',
            'is_active',
        ])
        ->withTimestamps();
}




public function appointments()
{
    return $this->hasMany(MedicalAppointment::class, 'service_id')
        ->latest('appointment_date')
        ->latest('starts_at');
}
}