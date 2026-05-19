<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantTableServiceRequest extends Model
{
    protected $table = 'restaurant_table_service_requests';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'table_id',
        'type',
        'status',
        'table_number',
        'table_name',
        'guest_token',
        'notes',
        'seen_at',
        'done_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
        'done_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'waiter' => 'طلب الجرسون',
            'cash' => 'طلب الحساب',
            default => $this->type,
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new' => 'جديد',
            'seen' => 'تمت المشاهدة',
            'done' => 'تم التنفيذ',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'new' => 'bg-warning text-dark',
            'seen' => 'bg-info text-dark',
            'done' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}