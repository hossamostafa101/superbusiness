<?php

// app/Support/Affiliate.php
namespace App\Support;

use App\Models\SiteSetting;

class Affiliate {
  public static function amazonUrl(string $productUrl): string {
    $amz = SiteSetting::get('amazon', []);
    $tag = $amz['tag'] ?? null;
    if (!$tag) return $productUrl;
    // أضف tag مع الحفاظ على أي كويري سابق
    $sep = str_contains($productUrl,'?') ? '&' : '?';
    return $productUrl . $sep . 'tag=' . urlencode($tag);
  }
}
