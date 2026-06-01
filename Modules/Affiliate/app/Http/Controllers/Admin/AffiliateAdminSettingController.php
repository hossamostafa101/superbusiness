<?php

namespace Modules\Affiliate\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Affiliate\Models\AffiliateSetting;

class AffiliateAdminSettingController extends Controller
{
    public function edit()
    {
        $settings = AffiliateSetting::current();

        return view('affiliate::admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = AffiliateSetting::current();

        $data = $request->validate([
            'commission_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'commission_value' => ['required', 'numeric', 'min:0'],

            'hold_days' => ['required', 'integer', 'min:0', 'max:365'],
            'minimum_withdrawal_amount' => ['required', 'numeric', 'min:0'],

            'signup_bonus_enabled' => ['nullable', 'boolean'],
            'signup_bonus_amount' => ['required', 'numeric', 'min:0'],

            'currency' => ['required', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $settings->update([
            'commission_type' => $data['commission_type'],
            'commission_value' => $data['commission_value'],

            'hold_days' => $data['hold_days'],
            'minimum_withdrawal_amount' => $data['minimum_withdrawal_amount'],

            'signup_bonus_enabled' => $request->boolean('signup_bonus_enabled'),
            'signup_bonus_amount' => $data['signup_bonus_amount'],

            'currency' => strtoupper($data['currency']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم تحديث إعدادات برنامج الشركاء.');
    }
}