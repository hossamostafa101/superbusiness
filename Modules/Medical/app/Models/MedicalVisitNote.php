<?php

namespace Modules\Medical\Models;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalVisitNote extends Model
{
    protected $table = 'medical_visit_notes';

    protected $fillable = [
        'workspace_id',
        'visit_id',
        'staff_id',
        'type',
        'note',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(MedicalVisit::class, 'visit_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(MedicalStaff::class, 'staff_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'general' => 'عام',
            'complaint' => 'شكوى',
            'diagnosis' => 'تشخيص',
            'treatment' => 'خطة علاج',
            'follow_up' => 'متابعة',
            'internal' => 'داخلي',
            default => 'ملاحظة',
        };
    }
}