<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalPrescription extends Model
{
    protected $table = 'medical_prescriptions';

    protected $fillable = [
        'workspace_id',
        'visit_id',
        'patient_id',
        'staff_id',
        'prescription_number',
        'issued_at',
        'diagnosis_summary',
        'instructions',
        'notes',
        'status',
        'patient_name',
        'staff_name',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(MedicalVisit::class, 'visit_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(MedicalPatient::class, 'patient_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(MedicalStaff::class, 'staff_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MedicalPrescriptionItem::class, 'prescription_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'مسودة',
            'issued' => 'صادرة',
            'cancelled' => 'ملغية',
            default => 'غير معروف',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'issued' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-light text-dark',
        };
    }
}