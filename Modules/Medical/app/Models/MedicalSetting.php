<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalSetting extends Model
{
    protected $table = 'medical_settings';

    protected $fillable = [
        'workspace_id',
        'facility_type',
        'display_name',
        'description',
        'booking_enabled',
        'online_payment_enabled',
        'patient_portal_enabled',
        'default_currency',
        'default_visit_duration',
        'allow_patient_files',
        'allow_results_download',
        'whatsapp_notifications_enabled',
        'sms_notifications_enabled',
        'primary_color',
        'secondary_color',
    ];

    protected $casts = [
        'booking_enabled' => 'boolean',
        'online_payment_enabled' => 'boolean',
        'patient_portal_enabled' => 'boolean',
        'allow_patient_files' => 'boolean',
        'allow_results_download' => 'boolean',
        'whatsapp_notifications_enabled' => 'boolean',
        'sms_notifications_enabled' => 'boolean',
        'default_visit_duration' => 'integer',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function facilityTypeLabel(): string
    {
        return match ($this->facility_type) {
            'clinic' => 'عيادة',
            'medical_center' => 'مركز طبي',
            'hospital' => 'مستشفى',
            'lab' => 'معمل تحاليل',
            'scan_center' => 'مركز أشعة',
            default => 'منشأة طبية',
        };
    }
}