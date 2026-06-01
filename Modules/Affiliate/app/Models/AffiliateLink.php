<?php

namespace Modules\Affiliate\Models;

use App\Models\Specification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateLink extends Model
{
    protected $table = 'affiliate_links';

    protected $fillable = [
        'affiliate_profile_id',
        'specification_id',
        'title',
        'slug',
        'target_url',
        'tracking_url',
        'clicks_count',
        'registrations_count',
        'conversions_count',
        'is_active',
    ];

    protected $casts = [
        'clicks_count' => 'integer',
        'registrations_count' => 'integer',
        'conversions_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function affiliateProfile(): BelongsTo
    {
        return $this->belongsTo(AffiliateProfile::class, 'affiliate_profile_id');
    }

    public function specification(): BelongsTo
    {
        return $this->belongsTo(Specification::class, 'specification_id');
    }
}