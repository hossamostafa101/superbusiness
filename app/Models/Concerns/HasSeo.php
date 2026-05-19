<?php
// app/Models/Concerns/HasSeo.php
namespace App\Models\Concerns;

use App\Models\SeoMeta;

trait HasSeo
{
    public function seo(){ return $this->morphOne(SeoMeta::class, 'seoable'); }

    // Fallback helpers (used by the frontend partial)
    public function getSeoTitleFallback(): ?string
    {
        return $this->title ?? $this->name ?? null;
    }

    public function getSeoDescriptionFallback(): ?string
    {
        // Try excerpt/summary-like fields
        $raw = $this->excerpt ?? $this->subtitle ?? null;
        if (!$raw && property_exists($this,'body')) {
            $raw = strip_tags((string)$this->body);
        }
        return $raw ? str($raw)->limit(160)->toString() : null;
    }

    public function getSeoImageFallback(): ?string
    {
        $img = $this->cover_image
            ?? $this->image_url
            ?? $this->thumb
            ?? null;

        return $img ? asset(str_replace('\\','/',$img)) : config('seo.fallback_image');
    }

    public function getCanonicalFallback(): ?string
    {
        // Prefer slug routes if exist
        if (isset($this->slug) && $this->slug) {
            // Guess route by type (adjust if you have named routes)
            $base = match (true) {
                $this instanceof \App\Models\NewsArticle => url('/news/'.$this->slug),
                $this instanceof \App\Models\Article     => url('/articles/'.$this->slug),
                $this instanceof \App\Models\Review      => url('/reviews/'.$this->slug),
                default                                   => url()->current(),
            };
            return $base;
        }
        return url()->current();
    }
}
