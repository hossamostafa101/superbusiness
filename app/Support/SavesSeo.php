<?php
// app/Support/SavesSeo.php
namespace App\Support;

use Illuminate\Http\Request;

trait SavesSeo
{
    protected function saveSeoFor($seoable, Request $request): void
    {
        $seo = (array) $request->input('seo', []);
        $seo['robots_noindex'] = !empty($seo['robots_noindex']);
        $seo['robots_nofollow'] = !empty($seo['robots_nofollow']);
        $seoable->seo()->updateOrCreate([], $seo);
    }
}
