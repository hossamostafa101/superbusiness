<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalVisit extends Model
{
    protected $table = 'medical_visits';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'appointment_id',
        'patient_id',
        'staff_id',
        'service_id',

        'visit_number',
        'visit_date',
        'started_at',
        'ended_at',

        'status',
        'visit_type',

        'chief_complaint',
        'diagnosis',
        'treatment_plan',

        'notes',
        'internal_notes',

        'patient_name',
        'staff_name',
        'service_name',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(MedicalBranch::class, 'branch_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(MedicalAppointment::class, 'appointment_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(MedicalPatient::class, 'patient_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(MedicalStaff::class, 'staff_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(MedicalService::class, 'service_id');
    }

    public function visitNotes(): HasMany
    {
        return $this->hasMany(MedicalVisitNote::class, 'visit_id')
            ->latest('id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'مفتوحة',
            'in_progress' => 'جاري الكشف',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغية',
            default => 'غير معروف',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'bg-primary',
            'in_progress' => 'bg-dark',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function visitTypeLabel(): string
    {
        return match ($this->visit_type) {
            'consultation' => 'كشف',
            'follow_up' => 'متابعة',
            'procedure' => 'إجراء',
            'lab' => 'معمل',
            'scan' => 'أشعة',
            'emergency' => 'طوارئ',
            default => 'أخرى',
        };
    }





    public function prescriptions()
{
    return $this->hasMany(MedicalPrescription::class, 'visit_id')
        ->latest('issued_at');
}
}