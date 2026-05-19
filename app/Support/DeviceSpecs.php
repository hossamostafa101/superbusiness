<?php
// app/Support/DeviceSpecs.php
namespace App\Support;

use App\Models\Device;
use Illuminate\Support\Str;

class DeviceSpecs
{
    public static function sections(Device $d): array
    {
        // --- helpers (same as your device page) ---
        $join = function (array $parts, string $sep = ', ') {
            $parts = array_filter(array_map(function ($v) {
                if (is_array($v)) $v = implode(', ', array_filter($v, fn($x)=>trim((string)$x) !== ''));
                $v = trim((string)$v);
                return $v === '' ? null : $v;
            }, $parts));
            return $parts ? implode($sep, $parts) : '—';
        };

        $flag = function ($v) {
            $ok   = (bool)$v;
            $txt  = $ok ? 'متاح' : 'غير متاح';
            $icon = $ok ? 'bi-check-circle' : 'bi-x-circle';
            $cls  = $ok ? 'flag-yes' : 'flag-no';
            return "<span class=\"flag $cls\"><i class=\"bi $icon\"></i>$txt</span>";
        };
        $R = fn($label, $value, $raw=false) => $raw ? [$label, (string)$value, 'raw'] : [$label, (string)$value];

        // --- build composed strings (copied from your controller) ---
        $tech = $join([$d->net_2g?'GSM':null,$d->net_3g?'HSPA':null,$d->net_4g?'LTE':null,$d->net_5g?'5G':null],' / ');
        $speed = $join([$d->dl_4g_mbps ? "4G up to {$d->dl_4g_mbps} Mbps" : null, $d->dl_5g_gbps ? "5G up to {$d->dl_5g_gbps} Gbps" : null],' / ');
        $sim = $join([$d->sim_type,$d->sim_count ? "{$d->sim_count} SIM" : null,$d->sim_hybrid ? 'Hybrid' : null],' • ');
        $build = $join([$d->build_frame ? "Frame: {$d->build_frame}" : null,$d->build_back ? "Back: {$d->build_back}" : null],' | ');
        $size = $d->display_size_in ? number_format((float)$d->display_size_in, 1).'”' : null;

        $dispType = $join([
            $d->display_type,
            $d->display_refresh_hz      ? "{$d->display_refresh_hz}Hz"        : null,
            $d->display_pwm_hz          ? "{$d->display_pwm_hz}Hz PWM"        : null,
            $d->touch_sampling_hz       ? "{$d->touch_sampling_hz}Hz touch"   : null,
            $d->display_brightness_peak_nits ? "peak {$d->display_brightness_peak_nits} nits" : null,
            $d->display_coverage_pct    ? "≈{$d->display_coverage_pct}% STB"  : null,
        ], ', ');

        $resolution = $join([$d->display_resolution,$d->display_aspect_ratio,$d->display_ppi ? "~{$d->display_ppi} ppi" : null],' • ');

        $internal = $join([$d->storage_gb ? "{$d->storage_gb} GB" : null,$d->ram_mb ? (int) round($d->ram_mb/1024).' GB RAM' : null], ', ');

        $charging = $join([
            $d->chg_wired_w    ? "{$d->chg_wired_w}W wired"     : null,
            $d->chg_wireless_w ? "{$d->chg_wireless_w}W wireless" : null,
            $d->chg_reverse_w  ? "{$d->chg_reverse_w}W reverse" : null,
        ], ', ');

        $cpuLine = $join([$d->cpu,$d->cpu_cores ? "{$d->cpu_cores}-core" : null,$d->cpu_max_ghz ? "up to {$d->cpu_max_ghz} GHz" : null,$d->process_nm ? "{$d->process_nm} nm" : null],' • ');

        $release = $d->released_at?->format('M Y');

        // --- sections (same as your latest) ---
        return [
            'LAUNCH' => [
                $R('Announced',   $d->announced ?: '—'),
                $R('Released',    $release ?: '—'),
                $R('Status',      $d->status ?: '—'),
                $R('Category',    $d->category ?: '—'),
                $R('Regions',     $d->market_regions   ? implode(', ', (array)$d->market_regions)   : '—'),
                $R('Countries',   $d->market_countries ? implode(', ', (array)$d->market_countries) : '—'),
            ],
            'BODY' => [
                $R('Dimensions',  $d->dimensions_mm ?: $join([$d->length_mm, $d->width_mm, $d->thickness_mm ? "{$d->thickness_mm} mm" : null], ' x ')),
                $R('Weight',      $d->weight_g ? "{$d->weight_g} g" : '—'),
                $R('Build',       $build),
                $R('SIM',         $sim),
                $R('IP rating',   $d->ip_rating ?: '—'),
                $R('MIL-STD',     $d->mil_std ?: '—'),
            ],
            'DISPLAY' => [
                $R('Type',        $dispType),
                $R('Size',        $size ?: '—'),
                $R('Resolution',  $resolution),
                $R('Protection',  $d->display_protection ?: '—'),
                $R('Extras',      $d->display_extras ? implode(', ', (array)$d->display_extras) : '—'),
            ],
            'PLATFORM' => [
                $R('OS',          $join([$d->os, $d->ui_skin], ' • ')),
                $R('Chipset',     $d->chipset ?: '—'),
                $R('CPU',         $cpuLine),
                $R('GPU',         $d->gpu ?: '—'),
            ],
            'MEMORY' => [
                $R('Card slot',   $flag($d->external_sd), true),
                $R('Internal',    $internal),
                $R('Storage Type',$d->storage_type ?: '—'),
                $R('RAM',         $d->ram_mb ? (int) round($d->ram_mb/1024).' GB' : '—'),
                $R('RAM Type',    $d->ram_type ?: '—'),
                $R('AnTuTu',      $d->antutu_score ? "{$d->antutu_score} (v{$d->antutu_v})" : '—'),
            ],
            'MAIN CAMERA' => [
                $R('Setup',       $d->rear_cams_count ? "{$d->rear_cams_count} cams" : '—'),
                $R('Main',        $d->camera_main_mp ? "{$d->camera_main_mp} MP" : '—'),
                $R('Optical zoom',$d->rear_optical_zoom_x ? "{$d->rear_optical_zoom_x}x" : '—'),
                $R('OIS / EIS',   ($d->rear_ois?'OIS':'—').' / '.($d->rear_eis?'EIS':'—')),
                $R('Features',    $d->rear_features ? implode(', ', (array)$d->rear_features) : '—'),
                $R('Video',       $d->video_rear ?: '—'),
            ],
            'SELFIE CAMERA' => [
                $R('Setup',       $d->selfie_cams_count ? "{$d->selfie_cams_count} cam(s)" : '—'),
                $R('Single',      $d->camera_selfie_mp ?: '—'),
                $R('Features',    $d->selfie_features ? implode(', ', (array)$d->selfie_features) : '—'),
                $R('Video',       $d->video_selfie ?: '—'),
            ],
            'SOUND' => [
                $R('Loudspeaker', $d->stereo_speakers ? 'Yes, stereo speakers' : 'Yes'),
                $R('3.5mm jack',  $flag($d->jack_3_5mm), true),
                $R('Extras',      $d->audio_features ? implode(', ', (array)$d->audio_features) : '—'),
            ],
            'COMMS' => [
                $R('WLAN',      $d->wifi_version ? "Wi-Fi {$d->wifi_version}" : '—'),
                $R('Bluetooth', $d->bluetooth_version ?: '—'),
                $R('GPS',       $flag($d->gps), true),
                $R('NFC',       $flag($d->nfc), true),
                $R('Infrared',  $flag($d->ir_blaster), true),
                $R('USB',       $d->usb_type ?: '—'),
            ],
            'FEATURES' => [
                $R('Fingerprint', $d->fingerprint ?: '—'),
                $R('Face Unlock', $flag($d->face_unlock), true),
            ],
            'BATTERY' => [
                $R('Type',       $d->battery_type ?: '—'),
                $R('Capacity',   $d->battery_mah ? "{$d->battery_mah} mAh" : '—'),
                $R('Charging',   $charging),
                $R('Removable',  $flag($d->battery_removable), true),
            ],
            'MISC' => [
                $R('Colors', $d->colors ? implode(', ', (array)$d->colors) : '—'),
                $R('AKA',    $d->aka ? implode(', ', (array)$d->aka) : '—'),
                $R('In box', $d->in_box ? implode(' • ', (array)$d->in_box) : '—'),
            ],
            'PERFORMANCE / GAMES' => [
                $R('PUBG FPS',      $d->game_pubg_fps ?: '—'),
                $R('PUBG Settings', $d->game_pubg_settings ?: '—'),
                $R('AnTuTu',        $d->antutu_score ? "{$d->antutu_score} (v{$d->antutu_v})" : '—'),
            ],
            'NETWORK' => [
                $R('Technology', $tech),
                $R('2G bands',   $d->net_2g ?: '—'),
                $R('3G bands',   $d->net_3g ?: '—'),
                $R('4G bands',   $d->net_4g ?: '—'),
                $R('5G bands',   $d->net_5g ?: '—'),
                $R('GPRS',       $flag($d->gprs), true),
                $R('EDGE',       $flag($d->edge), true),
                $R('Speed',      $speed),
            ],
            'PRICE' => [
                $R('Amazon Price', $d->amazon_price_formatted ?: '—'),
                $R('Checked at',   $d->amazon_price_checked_at?->format('Y-m-d H:i') ?: '—'),
                $R('Market',       strtoupper($d->amazon_market ?? '') ?: '—'),
                $R('ASIN',         $d->amazon_asin ?: '—'),
            ],
        ];
    }
}
