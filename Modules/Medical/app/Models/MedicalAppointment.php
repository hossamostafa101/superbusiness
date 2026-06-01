<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalAppointment extends Model
{
    protected $table = 'medical_appointments';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'patient_id',
        'service_id',
        'staff_id',

        'appointment_number',
        'appointment_date',
        'starts_at',
        'ends_at',

        'status',
        'payment_status',
        'source',

        'patient_name',
        'patient_phone',
        'patient_email',

        'service_name',
        'staff_name',

        'price',
        'currency',

        'notes',
        'internal_notes',

        'cancelled_at',
        'cancelled_reason',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'cancelled_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(MedicalBranch::class, 'branch_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(MedicalPatient::class, 'patient_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(MedicalService::class, 'service_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(MedicalStaff::class, 'staff_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'في الانتظار',
            'confirmed' => 'مؤكد',
            'checked_in' => 'وصل',
            'in_progress' => 'جاري الكشف',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'no_show' => 'لم يحضر',
            default => 'غير معروف',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'confirmed' => 'bg-primary',
            'checked_in' => 'bg-info text-dark',
            'in_progress' => 'bg-dark',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'no_show' => 'bg-secondary',
            default => 'bg-light text-dark',
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'غير مدفوع',
            'partially_paid' => 'مدفوع جزئيًا',
            'paid' => 'مدفوع',
            'refunded' => 'مسترد',
            default => 'غير معروف',
        };
    }






    public function visit()
{
    return $this->hasOne(MedicalVisit::class, 'appointment_id');
}
}