<?php

namespace Modules\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AffiliateWithdrawal extends Model
{
    protected $table = 'affiliate_withdrawals';

    protected $fillable = [
        'affiliate_profile_id',
        'withdrawal_number',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_details',
        'requested_at',
        'approved_at',
        'paid_at',
        'rejected_at',
        'admin_notes',
        'affiliate_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function affiliateProfile(): BelongsTo
    {
        return $this->belongsTo(AffiliateProfile::class, 'affiliate_profile_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'requested' => 'مطلوب',
            'approved' => 'مقبول',
            'paid' => 'مدفوع',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي',
            default => 'غير معروف',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'requested' => 'bg-warning text-dark',
            'approved' => 'bg-info text-dark',
            'paid' => 'bg-success',
            'rejected' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-light text-dark',
        };
    }


    public function commissions(): BelongsToMany
{
    return $this->belongsToMany(
        AffiliateCommission::class,
        'affiliate_withdrawal_commissions',
        'affiliate_withdrawal_id',
        'affiliate_commission_id'
    )->withPivot('amount')->withTimestamps();
}
}