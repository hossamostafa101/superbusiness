<?php

namespace Modules\Affiliate\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Affiliate\Models\AffiliateCommission;
use Modules\Affiliate\Models\AffiliateProfile;
use Modules\Affiliate\Models\AffiliateSetting;

class AffiliateRegistrationService
{
    public function register(array $data): AffiliateProfile
    {
        $settings = AffiliateSetting::current();

        return DB::transaction(function () use ($data, $settings) {
            $user = User::query()
                ->where('email', $data['email'])
                ->first();

            if (! $user) {
                $user = User::query()->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
            }

            $profile = AffiliateProfile::query()
                ->where('user_id', $user->id)
                ->first();

            if ($profile) {
                return $profile;
            }

            $profile = AffiliateProfile::query()->create([
                'user_id' => $user->id,
                'code' => $this->generateCode($data['name'] ?? $user->name),
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'] ?? null,

                // ممكن تخليها pending لو عايز موافقة أدمن
                'status' => 'active',

                'registered_at' => now(),
                'approved_at' => now(),
            ]);

            if ($settings->signup_bonus_enabled && (float) $settings->signup_bonus_amount > 0) {
                AffiliateCommission::query()->create([
                    'affiliate_profile_id' => $profile->id,

                    'type' => 'signup_bonus',
                    'base_amount' => 0,

                    'commission_type' => 'fixed',
                    'commission_value' => $settings->signup_bonus_amount,
                    'amount' => $settings->signup_bonus_amount,
                    'currency' => $settings->currency,

                    // البونص متاح لكنه لا يسحب إلا عند وصول الحد الأدنى
                    'status' => 'available',

                    'earned_at' => now(),
                    'available_at' => now(),

                    'notes' => 'Signup bonus',
                ]);

                $profile->recalculateBalances();
            }



            app(\Modules\Affiliate\Services\AffiliateLinkService::class)
    ->ensureDefaultLinksForProfile($profile);

    
            return $profile;
        });
    }

    private function generateCode(string $name): string
    {
        $prefix = Str::upper(Str::slug($name, ''));

        if ($prefix === '') {
            $prefix = 'AFF';
        }

        $prefix = Str::limit($prefix, 8, '');

        do {
            $code = $prefix . random_int(1000, 9999);
        } while (
            AffiliateProfile::query()
                ->where('code', $code)
                ->exists()
        );

        return $code;
    }
}