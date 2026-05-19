<?php

namespace App\Services\Public\RestaurantMenu;

use Illuminate\Support\Facades\Hash;

class RestaurantInvoicePinService
{
    public function generate(): array
    {
        $first = random_int(10, 99);
        $second = random_int(10, 99);

        $plain = $first . $second;
        $display = $first . '-' . $second;

        return [
            'plain' => $plain,
            'display' => $display,
            'hash' => Hash::make($plain),
            'hint' => $display,
        ];
    }

    public function normalize(?string $pin): string
    {
        return preg_replace('/\D+/', '', (string) $pin);
    }

    public function verify(?string $pin, string $hash): bool
    {
        $normalized = $this->normalize($pin);

        if (strlen($normalized) !== 4) {
            return false;
        }

        return Hash::check($normalized, $hash);
    }
}