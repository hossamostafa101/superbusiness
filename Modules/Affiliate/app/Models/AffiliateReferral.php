<?php

namespace Modules\Affiliate\Models;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Specification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateReferral extends Model
{
    protected $table = 'affiliate_referrals';

    protected $fillable = [
        'affiliate_profile_id',
        'affiliate_link_id',
        'referred_user_id',
        'workspace_id',
        'specification_id',
        'ref_code',
        'landing_url',
        'ip_address',
        'user_agent',
        'status',
        'clicked_at',
        'registered_at',
        'converted_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'registered_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function affiliateProfile(): BelongsTo
    {
        return $this->belongsTo(AffiliateProfile::class, 'affiliate_profile_id');
    }

    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class, 'affiliate_link_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function specification(): BelongsTo
    {
        return $this->belongsTo(Specification::class, 'specification_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_referral_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'clicked' => 'زيارة',
            'registered' => 'سجل',
            'converted' => 'تحول لعميل',
            'active_subscriber' => 'مشترك نشط',
            'cancelled' => 'ملغي',
            default => 'غير معروف',
        };
    }
}