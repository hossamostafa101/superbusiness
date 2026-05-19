<?php

namespace App\Models\Concerns;

trait HasI18n
{
    // ترتيب السقوط الاحتياطي
    protected array $i18nFallbackOrder = ['ar','en','fr'];

    /**
     * قراءة قيمة مترجمة مع fallback
     */
    public function tr(string $base, $fallback = null, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $key    = $base . '_i18n';

        // نستخدم الـ attributes مباشرة لتفادي الـ recursion
        $bag = $this->attributes[$key] ?? null;

        if (is_string($bag)) {
            $bag = json_decode($bag, true) ?: [];
        }

        if (is_array($bag)) {
            // 1) اللغة الحالية
            if (!empty($bag[$locale])) {
                return $bag[$locale];
            }
            // 2) ترتيب اللغات الاحتياطي
            foreach ($this->i18nFallbackOrder as $l) {
                if (!empty($bag[$l])) {
                    return $bag[$l];
                }
            }
        }

        // fallback للعمود الأصلي (خام) بدون استدعاء accessor
        $old = $this->attributes[$base] ?? null;

        return $old ?? $fallback;
    }

    /**
     * تعيين حقل مترجم واحد (base_i18n)
     *
     * $value ممكن تكون:
     * - ['ar' => '...', 'en' => '...']
     * - أو string واحدة للغة حالية
     */
    public function setTr(string $base, array|string|null $value, ?string $locale = null): void
    {
        $key = $base . '_i18n';

        $existing = $this->attributes[$key] ?? null;
        if (is_string($existing)) {
            $existing = json_decode($existing, true) ?: [];
        }
        $bag = is_array($existing) ? $existing : [];

        if (is_array($value)) {
            // value فيها كل اللغات
            $bag = [];
            foreach ($value as $lc => $val) {
                if ($val !== null && $val !== '') {
                    $bag[$lc] = $val;
                }
            }
        } else {
            // قيمة مفردة للّغة الحالية
            $loc = $locale ?: app()->getLocale();
            if ($value === null || $value === '') {
                unset($bag[$loc]);
            } else {
                $bag[$loc] = $value;
            }
        }

        if (empty($bag)) {
            $this->attributes[$key] = null;
        } else {
            $this->attributes[$key] = json_encode($bag, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * تعيين مجموعة حقول مترجمة مرة واحدة (تُستدعى من الكنترولر)
     *
     * متوقَّع:
     * [
     *   'title'    => ['ar'=>'..','en'=>'..','fr'=>'..'],
     *   'subtitle' => [...],
     *   'excerpt'  => [...],
     *   'body'     => [...],
     *   'pros'     => ['ar'=>['..','..'], 'en'=>[...]],
     *   'cons'     => ...
     * ]
     */
    public function setLocalizedPayload(array $payload): void
    {
        foreach (['title','subtitle','excerpt','body','pros','cons'] as $field) {
            if (array_key_exists($field, $payload)) {
                $this->setTr($field, $payload[$field]);
            }
        }
    }
}
