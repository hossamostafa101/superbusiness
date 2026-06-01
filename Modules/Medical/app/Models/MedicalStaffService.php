<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalStaffService extends Model
{
    protected $table = 'medical_staff_services';

    protected $fillable = [
        'workspace_id',
        'staff_id',
        'service_id',
        'price_override',
        'duration_override',
        'is_active',
    ];

    protected $casts = [
        'price_override' => 'decimal:2',
        'duration_override' => 'integer',
        'is_active' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(MedicalStaff::class, 'staff_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(MedicalService::class, 'service_id');
    }

    public function effectivePrice(): ?float
    {
        if ($this->price_override !== null) {
            return (float) $this->price_override;
        }

        return $this->service?->price !== null
            ? (float) $this->service->price
            : null;
    }

    public function effectiveDuration(): ?int
    {
        if ($this->duration_override !== null) {
            return (int) $this->duration_override;
        }

        return $this->service?->duration_minutes;
    }
}