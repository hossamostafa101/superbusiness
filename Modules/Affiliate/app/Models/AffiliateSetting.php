<?php

namespace Modules\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateSetting extends Model
{
    protected $table = 'affiliate_settings';

    protected $fillable = [
        'commission_type',
        'commission_value',
        'hold_days',
        'minimum_withdrawal_amount',
        'signup_bonus_enabled',
        'signup_bonus_amount',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'commission_value' => 'decimal:2',
        'minimum_withdrawal_amount' => 'decimal:2',
        'signup_bonus_enabled' => 'boolean',
        'signup_bonus_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()
            ->where('is_active', true)
            ->latest('id')
            ->first()
            ?: static::query()->create([
                'commission_type' => 'percentage',
                'commission_value' => 20,
                'hold_days' => 45,
                'minimum_withdrawal_amount' => 500,
                'signup_bonus_enabled' => true,
                'signup_bonus_amount' => 200,
                'currency' => 'EGP',
                'is_active' => true,
            ]);
    }
}