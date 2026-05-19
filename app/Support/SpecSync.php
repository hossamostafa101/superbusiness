<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use App\Models\Device;

class SpecSync
{
    /**
     * Take any “specs” array (scraped JSON, labels from page, mixed keys),
     * normalize & map to devices table columns. Returns [changed columns].
     *
     * Example inputs it understands (case/spacing doesn’t matter):
     *  - "Size" => "6.7 inches"
     *  - "Resolution" => "1440 x 3120 pixels (~522 ppi)"
     *  - "OS" => "Android 15"
     *  - "Chipset" => "Dimensity 9300"
     *  - "RAM" => "12/16 GB"
     *  - "Storage" => "256GB/512GB/1TB"
     *  - "Charging" => "80W wired, 50W wireless, 10W reverse"
     *  - "WLAN" / "Wi-Fi" => "802.11 a/b/g/n/ac/6e"
     *  - "Bluetooth" => "5.3"
     *  - "USB" => "USB Type-C 3.2"
     *  - "2G bands" => "...", etc.
     */
    public static function sync(Device $device, array $rawSpecs): array
    {
        // 0) Flatten & lowercase keys for fuzzy matching
        $bag = self::flatten($rawSpecs);

        // Convenience getter that tries many aliases
        $get = fn(array $aliases) => self::firstValue($bag, $aliases);

        // Build update array, only keeping columns that exist
        $u = [];

        // ===== LAUNCH
        if ($v = $get(['announced','launch.announced'])) {
            $u['announced'] = self::toYear($v);
        }
        if ($v = $get(['released','launch.released'])) {
            $u['released_at'] = self::toDate($v);
        }
        if ($v = $get(['status','launch.status'])) {
            $u['status'] = self::clean($v, 100);
        }
        if ($v = $get(['category','launch.category'])) {
            $u['category'] = self::clean($v, 50);
        }

        // ===== BODY
        if ($v = $get(['dimensions','body.dimensions'])) {
            $u['dimensions_mm'] = self::preferMm($v); // keep raw text if already mm; otherwise keep given string
            // also try to split into L/W/T if available
            [$L,$W,$T] = self::splitLWT($v);
            $u += array_filter([
                'length_mm'    => $L,
                'width_mm'     => $W,
                'thickness_mm' => $T,
            ], fn($x)=>!is_null($x));
        }
        if ($v = $get(['weight','body.weight'])) {
            $u['weight_g'] = self::toNumber($v, 'g');
        }
        if ($v = $get(['sim','body.sim']))       $u['sim_type'] = self::clean($v, 50);
        if ($v = $get(['ip rating','ip_rating'])) $u['ip_rating'] = self::clean($v, 20);
        if ($v = $get(['mil-std','mil_std']))     $u['mil_std']   = self::toBoolish($v);

        // ===== DISPLAY
        if ($v = $get(['display.size','size','displaysize','display size'])) {
            $u['display_size_in'] = self::toInches($v);
        }
        if ($v = $get(['display.resolution','resolution','displayresolution'])) {
            [$res,$ppi] = self::splitResolution($v);
            $u['display_resolution'] = $res;
            if (!is_null($ppi) && Schema::hasColumn('devices','display_ppi')) {
                $u['display_ppi'] = $ppi;
            }
        }
        if ($v = $get(['display.type','displaytype','type']))      $u['display_type'] = self::clean($v, 80);
        if ($v = $get(['display.protection','protection']))        $u['display_protection'] = self::clean($v, 80);
        if ($v = $get(['refresh','refresh rate']))                 $u['display_refresh_hz'] = self::toHz($v);

        // ===== PLATFORM
        if ($v = $get(['os','platform.os']))                       $u['os'] = self::clean($v, 191);
        if ($v = $get(['chipset','platform.chipset']))             $u['chipset'] = self::clean($v, 191);
        if ($v = $get(['cpu','platform.cpu']))                     $u['cpu'] = self::clean($v, 255);
        if ($v = $get(['gpu','platform.gpu']))                     $u['gpu'] = self::clean($v, 191);

        // ===== MEMORY
        if ($v = $get(['ram','memory.ram','ramsize-hl']))          $u['ram_mb'] = self::toRamMb($v);
        if ($v = $get(['internal','storage','memory.internal']))   $u['storage_gb'] = self::toStorageGb($v);
        if ($v = $get(['ram type']))                               $u['ram_type'] = self::clean($v, 50);
        if ($v = $get(['storage type']))                           $u['storage_type'] = self::clean($v, 50);
        if ($v = $get(['card slot','external sd']))                $u['external_sd'] = self::toYesNo($v);

        // ===== MAIN/SELFIE (only easy ones)
        if ($v = $get(['main camera','camera.main','camera mp','camerapixels-hl'])) {
            $u['camera_main_mp'] = self::clean($v, 50);
        }

        // ===== SOUND / COMMS
        if ($v = $get(['loudspeaker']))                            $u['stereo_speakers'] = self::toYesNo($v, allowStereo:true);
        if ($v = $get(['3.5mm jack','jack']))                      $u['jack_3_5mm'] = self::toYesNo($v);

        if ($v = $get(['wlan','wi-fi','wifi']))                    $u['wifi_version'] = self::toWifiVer($v);
        if ($v = $get(['bluetooth']))                              $u['bluetooth_version'] = self::toBtVer($v);
        if ($v = $get(['gps']))                                    $u['gps'] = self::toYesNo($v);
        if ($v = $get(['nfc']))                                    $u['nfc'] = self::toYesNo($v);
        if ($v = $get(['infrared','ir']))                          $u['ir_blaster'] = self::toYesNo($v);
        if ($v = $get(['usb']))                                    $u['usb_type'] = self::clean($v, 30);

        if ($v = $get(['fingerprint']))                            $u['fingerprint'] = self::toFingerprint($v);
        if ($v = $get(['face unlock']))                            $u['face_unlock'] = self::toYesNo($v);

        // ===== BATTERY
        if ($v = $get(['battery.type']))                           $u['battery_type'] = self::clean($v, 50);
        if ($v = $get(['battery','capacity','batsize-hl']))        $u['battery_mah'] = self::toNumber($v, 'mah');
        if ($v = $get(['charging'])) {
            [$wired,$wireless,$reverse] = self::splitCharging($v);
            $u += array_filter([
                'chg_wired_w'    => $wired,
                'chg_wireless_w' => $wireless,
                'chg_reverse_w'  => $reverse,
            ], fn($x)=>!is_null($x));
        }
        if ($v = $get(['removable']))                              $u['battery_removable'] = self::toYesNo($v);

        // ===== NETWORK (booleans + optional band strings)
        if ($v = $get(['2g bands','net2g'])) { $u['net_2g'] = 1; if (Schema::hasColumn('devices','bands_2g')) $u['bands_2g'] = trim($v); }
        if ($v = $get(['3g bands','net3g'])) { $u['net_3g'] = 1; if (Schema::hasColumn('devices','bands_3g')) $u['bands_3g'] = trim($v); }
        if ($v = $get(['4g bands','net4g'])) { $u['net_4g'] = 1; if (Schema::hasColumn('devices','bands_4g')) $u['bands_4g'] = trim($v); }
        if ($v = $get(['5g bands','net5g'])) { $u['net_5g'] = 1; if (Schema::hasColumn('devices','bands_5g')) $u['bands_5g'] = trim($v); }

        // Keep only columns that actually exist
        $u = array_filter($u, fn($val,$col)=>Schema::hasColumn('devices',$col), ARRAY_FILTER_USE_BOTH);

        // Apply to model (but return the diff so caller can decide to save)
        $changed = [];
        foreach ($u as $k=>$v) {
            if ($v !== '' && $device->{$k} !== $v) { $changed[$k] = $v; }
        }
        return $changed;
    }

    /* ================= helpers ================= */

    private static function flatten(array $a, string $prefix=''): array {
        $out = [];
        foreach ($a as $k=>$v) {
            $key = strtolower(trim(is_string($k)?$k:strval($k)));
            $full = ltrim($prefix.'.'.$key, '.');
            if (is_array($v)) $out += self::flatten($v, $full);
            else $out[$full] = trim((string)$v);
            // also store just the leaf key for forgiving lookup
            $out[$key] = $out[$full];
        }
        return $out;
    }
    private static function firstValue(array $bag, array $cands): ?string {
        foreach ($cands as $c) {
            $c = strtolower($c);
            if (array_key_exists($c, $bag) && $bag[$c] !== '') return $bag[$c];
        }
        return null;
    }
    private static function clean(string $s, int $max=255): string {
        $s = trim(preg_replace('/\s+/', ' ', $s));
        return mb_substr($s, 0, $max);
    }
    private static function toYear(string $s): ?int {
        if (preg_match('/(19|20)\d{2}/', $s, $m)) return (int)$m[0];
        return null;
    }
    private static function toDate(string $s): ?string {
        // Accept “2024, October 30”, “2024-10-30”, etc.
        $s = trim($s);
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $s, $m)) return $m[0];
        if (strtotime($s)) return date('Y-m-d', strtotime($s));
        return null;
    }
    private static function toInches(string $s): ?float {
        // “6.67 inches” /  “6.67\"”
        if (preg_match('/(\d+(\.\d+)?)/', $s, $m)) return round((float)$m[1], 2);
        return null;
    }
    private static function toHz(string $s): ?int {
        if (preg_match('/(\d+)\s*hz/i', $s, $m)) return (int)$m[1];
        if (preg_match('/\b(60|90|120|144|165|240)\b/', $s, $m)) return (int)$m[1];
        return null;
    }
    private static function toNumber(string $s, string $unit=''): ?int {
        // extracts the biggest integer in the string (for mAh, grams, etc.)
        preg_match_all('/\d{1,6}/', $s, $m);
        if (!$m[0]) return null;
        return (int)max(array_map('intval', $m[0]));
    }
    private static function toRamMb(string $s): ?int {
        // "12/16 GB" -> 16 GB -> MB
        if (preg_match_all('/(\d+(?:\.\d+)?)\s*gb/i', $s, $m)) {
            $gb = (float)max($m[1]);
            return (int)round($gb * 1024);
        }
        if (preg_match('/(\d+)\s*mb/i', $s, $m)) return (int)$m[1];
        return null;
    }
    private static function toStorageGb(string $s): ?int {
        // pick the largest number in GB/TB
        if (preg_match_all('/(\d+(?:\.\d+)?)\s*tb/i', $s, $m)) return (int)round(max($m[1]) * 1024);
        if (preg_match_all('/(\d+(?:\.\d+)?)\s*gb/i', $s, $m)) return (int)round(max($m[1]));
        return null;
    }
    private static function splitResolution(string $s): array {
        // returns [ "2560x1440", 522|null ]
        $res = null; $ppi = null;
        if (preg_match('/\b(\d{3,4}\s*[x×]\s*\d{3,4})\b/i', $s, $m)) $res = strtolower(str_replace('×','x',$m[1]));
        if (preg_match('/(~?\s*(\d{2,4}))\s*ppi/i', $s, $m)) $ppi = (int)$m[2];
        return [$res, $ppi];
    }
    private static function splitCharging(string $s): array {
        // read “80W wired, 50W wireless, 10W reverse”
        $wired=$wireless=$reverse=null;
        if (preg_match('/(\d+)\s*w.*wired/i', $s, $m))    $wired=(int)$m[1];
        if (preg_match('/(\d+)\s*w.*wireless/i', $s, $m)) $wireless=(int)$m[1];
        if (preg_match('/(\d+)\s*w.*reverse/i', $s, $m))  $reverse=(int)$m[1];
        // bare “80W” (assume wired)
        if (!$wired && preg_match('/(\d+)\s*w\b/i', $s, $m)) $wired=(int)$m[1];
        return [$wired,$wireless,$reverse];
    }
    private static function toWifiVer(string $s): ?string {
        // keep short form like “a/b/g/n/ac/6e/7”
        if (preg_match_all('/(a|b|g|n|ac|ax|be|6e|6|7)/i', $s, $m)) {
            $uniq = array_values(array_unique(array_map('strtolower',$m[1])));
            return implode('/', $uniq);
        }
        return self::clean($s, 15);
    }
    private static function toBtVer(string $s): ?string {
        if (preg_match('/\b(2\.[0-1]|3\.0|4\.[0-3]|5\.[0-4])\b/', $s, $m)) return $m[1];
        return self::clean($s, 10);
    }
    private static function toYesNo(string $s, bool $allowStereo=false): ?int {
        $s = strtolower($s);
        if ($allowStereo && str_contains($s,'stereo')) return 1;
        if (preg_match('/\b(yes|supported|present|available|with|has)\b/', $s)) return 1;
        if (preg_match('/\b(no|none|without|n\/a)\b/', $s)) return 0;
        return null;
    }
    private static function toFingerprint(string $s): ?string {
        $s = strtolower($s);
        return match(true) {
            str_contains($s,'under')    => 'under_display',
            str_contains($s,'display')  => 'under_display',
            str_contains($s,'side')     => 'side',
            str_contains($s,'rear')     => 'rear',
            str_contains($s,'front')    => 'front',
            str_contains($s,'no')       => 'none',
            default                     => null,
        };
    }
    private static function preferMm(string $s): string {
        // if we detect inches block, keep the given text as-is (you also store L/W/T separately)
        return trim($s);
    }
    private static function splitLWT(string $s): array {
        // returns [L,W,T] in mm if parseable
        if (preg_match_all('/(\d+(?:\.\d+)?)\s*mm/', strtolower($s), $m) && count($m[1]) >= 3) {
            $nums = array_map(fn($x)=>round((float)$x,2), $m[1]);
            return [$nums[0], $nums[1], $nums[2]];
        }
        return [null,null,null];
    }
    private static function toBoolish(string $s): ?int {
        $s = strtolower($s);
        return preg_match('/\b(yes|meet|passed|810|ip)\b/',$s) ? 1 : (preg_match('/\bno\b/',$s) ? 0 : null);
    }
}
