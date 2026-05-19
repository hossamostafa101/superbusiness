<?php
// app/Http/Middleware/SetLocale.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // قيمة افتراضية
        $locale = $request->cookie('locale', config('app.locale'));

        // استخدم السيشن لو متاح
        if ($request->hasSession()) {
            $locale = $request->session()->get('locale', $locale);
        }

        if (! in_array($locale, ['ar','en','fr'], true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        view()->share('dir', $locale === 'ar' ? 'rtl' : 'ltr');
        \Carbon\Carbon::setLocale($locale);

        return $next($request);
    }
}
