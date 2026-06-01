<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalPatient extends Model
{
    protected $table = 'medical_patients';

    protected $fillable = [
        'workspace_id',
        'patient_code',
        'first_name',
        'last_name',
        'full_name',
        'phone',
        'whatsapp_number',
        'email',
        'gender',
        'birth_date',
        'national_id',
        'insurance_provider',
        'insurance_number',
        'address',
        'city',
        'area',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_type',
        'allergies',
        'chronic_diseases',
        'notes',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function genderLabel(): string
    {
        return match ($this->gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => 'غير محدد',
        };
    }


    public function appointments()
{
    return $this->hasMany(MedicalAppointment::class, 'patient_id')
        ->latest('appointment_date')
        ->latest('starts_at');
}


public function visits()
{
    return $this->hasMany(MedicalVisit::class, 'patient_id')
        ->latest('visit_date')
        ->latest('id');
}







public function prescriptions()
{
    return $this->hasMany(MedicalPrescription::class, 'patient_id')
        ->latest('issued_at');
}
}