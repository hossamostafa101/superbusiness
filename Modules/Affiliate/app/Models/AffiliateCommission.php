<?php

namespace Modules\Affiliate\Models;

use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    protected $table = 'affiliate_commissions';

    protected $fillable = [
        'affiliate_profile_id',
        'affiliate_referral_id',
        'referred_user_id',
        'workspace_id',
        'subscription_id',
        'payment_id',
        'plan_id',
        'type',
        'base_amount',
        'commission_type',
        'commission_value',
        'amount',
        'currency',
        'status',
        'earned_at',
        'available_at',
        'paid_at',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'amount' => 'decimal:2',
        'earned_at' => 'datetime',
        'available_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function affiliateProfile(): BelongsTo
    {
        return $this->belongsTo(AffiliateProfile::class, 'affiliate_profile_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(AffiliateReferral::class, 'affiliate_referral_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'signup_bonus' => 'بونص تسجيل',
            'subscription_payment' => 'اشتراك مدفوع',
            'subscription_renewal' => 'تجديد اشتراك',
            'upgrade' => 'ترقية',
            'manual_bonus' => 'بونص يدوي',
            'adjustment' => 'تسوية',
            default => 'عمولة',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'قيد الانتظار',
            'available' => 'متاحة',
            'cancelled' => 'ملغية',
            'paid' => 'مدفوعة',
            default => 'غير معروف',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'available' => 'bg-success',
            'cancelled' => 'bg-danger',
            'paid' => 'bg-primary',
            default => 'bg-light text-dark',
        };
    }
}