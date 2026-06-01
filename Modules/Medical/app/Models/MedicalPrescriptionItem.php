<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalPrescriptionItem extends Model
{
    protected $table = 'medical_prescription_items';

    protected $fillable = [
        'workspace_id',
        'prescription_id',
        'medicine_name',
        'dosage',
        'frequency',
        'duration',
        'route',
        'instructions',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(MedicalPrescription::class, 'prescription_id');
    }
}