<?php

namespace Modules\Medical\Models;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalStaff extends Model
{
    protected $table = 'medical_staff';

    protected $fillable = [
        'workspace_id',
        'user_id',
        'branch_id',
        'department_id',
        'specialty_id',
        'role',
        'name',
        'slug',
        'title',
        'bio',
        'phone',
        'whatsapp_number',
        'email',
        'photo',
        'consultation_fee',
        'follow_up_fee',
        'currency',
        'default_slot_minutes',
        'accepts_online_booking',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'follow_up_fee' => 'decimal:2',
        'default_slot_minutes' => 'integer',
        'accepts_online_booking' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function roleLabel(): string
    {
        return match ($this->role) {
            'doctor' => 'طبيب',
            'nurse' => 'تمريض',
            'lab_technician' => 'فني معمل',
            'radiology_technician' => 'فني أشعة',
            'receptionist' => 'استقبال',
            'accountant' => 'محاسب',
            'admin' => 'مدير',
            default => 'أخرى',
        };
    }

    public function staffServices(): HasMany
{
    return $this->hasMany(MedicalStaffService::class, 'staff_id');
}

public function services(): BelongsToMany
{
    return $this->belongsToMany(
        MedicalService::class,
        'medical_staff_services',
        'staff_id',
        'service_id'
    )
        ->withPivot([
            'price_override',
            'duration_override',
            'is_active',
        ])
        ->withTimestamps();
}





public function workingHours()
{
    return $this->hasMany(MedicalStaffWorkingHour::class, 'staff_id')
        ->orderBy('day_of_week')
        ->orderBy('starts_at');
}




public function appointments()
{
    return $this->hasMany(MedicalAppointment::class, 'staff_id')
        ->latest('appointment_date')
        ->latest('starts_at');
}


public function visits()
{
    return $this->hasMany(MedicalVisit::class, 'staff_id')
        ->latest('visit_date')
        ->latest('id');
}





public function prescriptions()
{
    return $this->hasMany(MedicalPrescription::class, 'staff_id')
        ->latest('issued_at');
}
}