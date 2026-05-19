<?php

namespace App\Services;

use Imagick;
use ImagickPixel;

class ImageCutout
{
    /**
     * Remove white/near-white background and save as WEBP with alpha.
     * $fuzzPct ~0.08–0.16 controls how aggressive the white detection is.
     */
    public static function cutWhiteToAlpha(string $src, string $dest, float $fuzzPct = 0.12): bool
    {
        try {
            $blob = str_starts_with($src, 'http')
                ? @file_get_contents($src)
                : @file_get_contents($src);

            if (!$blob) return false;

            $im = new Imagick();
            $im->readImageBlob($blob);
            $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);

            $range = Imagick::getQuantumRange();
            $fuzz  = (int) round($fuzzPct * ($range['quantumRangeLong'] ?? 65535));

            // Make (near) white transparent
            $im->transparentPaintImage(new ImagickPixel('#ffffff'), 0.0, $fuzz, false);

            // Optional: trim leftover borders
            $im->trimImage(10);
            $im->setImagePage(0,0,0,0);

            $im->setImageFormat('webp');
            $im->setImageCompressionQuality(92);
            @mkdir(dirname($dest), 0775, true);
            $ok = $im->writeImage($dest);
            $im->clear(); $im->destroy();
            return (bool) $ok;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
