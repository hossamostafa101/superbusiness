<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalStaffWorkingHour extends Model
{
    protected $table = 'medical_staff_working_hours';

    protected $fillable = [
        'workspace_id',
        'staff_id',
        'branch_id',
        'day_of_week',
        'starts_at',
        'ends_at',
        'slot_minutes',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'slot_minutes' => 'integer',
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(MedicalBranch::class, 'branch_id');
    }

    public function dayLabel(): string
    {
        return match ((int) $this->day_of_week) {
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
            default => '-',
        };
    }
}