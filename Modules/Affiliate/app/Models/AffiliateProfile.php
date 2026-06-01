<?php

namespace Modules\Affiliate\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateProfile extends Model
{
    protected $table = 'affiliate_profiles';

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'email',
        'phone',
        'whatsapp_number',
        'status',
        'payment_method',
        'payment_details',
        'available_balance',
        'pending_balance',
        'paid_balance',
        'cancelled_balance',
        'registered_at',
        'approved_at',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'paid_balance' => 'decimal:2',
        'cancelled_balance' => 'decimal:2',
        'registered_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(AffiliateLink::class, 'affiliate_profile_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class, 'affiliate_profile_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_profile_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(AffiliateWithdrawal::class, 'affiliate_profile_id');
    }

    public function activeLinks(): HasMany
    {
        return $this->links()->where('is_active', true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'قيد المراجعة',
            'active' => 'نشط',
            'suspended' => 'موقوف',
            'rejected' => 'مرفوض',
            default => 'غير معروف',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'active' => 'bg-success',
            'suspended' => 'bg-danger',
            'rejected' => 'bg-secondary',
            default => 'bg-light text-dark',
        };
    }

    public function canRequestWithdrawal(?AffiliateSetting $settings = null): bool
    {
        $settings ??= AffiliateSetting::current();

        return (float) $this->available_balance >= (float) $settings->minimum_withdrawal_amount
            && $this->status === 'active';
    }

    public function recalculateBalances(): void
    {
        $available = $this->commissions()
            ->where('status', 'available')
            ->sum('amount');

        $pending = $this->commissions()
            ->where('status', 'pending')
            ->sum('amount');

        $paid = $this->commissions()
            ->where('status', 'paid')
            ->sum('amount');

        $cancelled = $this->commissions()
            ->where('status', 'cancelled')
            ->sum('amount');

        $this->update([
            'available_balance' => $available,
            'pending_balance' => $pending,
            'paid_balance' => $paid,
            'cancelled_balance' => $cancelled,
        ]);
    }
}