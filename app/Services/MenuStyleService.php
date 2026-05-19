<?php

namespace App\Services;

use App\Models\MenuSectionStyle;
use App\Models\Restaurant;
use App\Models\Branch;

class MenuStyleService
{
    /**
     * رجّع ستايل كل سكشن للمطعم (واختياريًا الفرع)
     *
     * - يعتمد على:
     *   - config('menu_templates') لتعريف السكاشن والتمبلتس المتاحة
     *   - جدول menu_section_styles لاختيارات المطعم/الفرع
     *
     * - الأولوية:
     *   1) لو فيه اختيار محدد للـ branch → نستخدمه
     *   2) لو فيه اختيار عام للمطعم (branch_id = null) → نستخدمه
     *   3) لو مفيش أي اختيار → نرجّع أول template من الـ config كـ Default
     */
    public function getStylesFor(Restaurant $restaurant, ?Branch $branch = null): array
    {
        $config = config('menu_templates', []);

        // لو مفيش أي تعريف في الكونفيج، رجّع مصفوفة فاضية
        if (empty($config) || !is_array($config)) {
            return [];
        }

        $baseQuery = MenuSectionStyle::query()
            ->where('restaurant_id', $restaurant->id);

        if ($branch) {
            // نجيب اختيارات المطعم العامة + الخاصة بالفرع
            $rows = $baseQuery
                ->where(function ($q) use ($branch) {
                    $q->whereNull('branch_id')
                      ->orWhere('branch_id', $branch->id);
                })
                ->get()
                ->groupBy('section_key');
        } else {
            // نجيب الاختيارات العامة بس (بدون فرع)
            $rows = $baseQuery
                ->whereNull('branch_id')
                ->get()
                ->groupBy('section_key');
        }

        $styles = [];

        foreach ($config as $sectionKey => $templates) {
            if (!is_array($templates) || empty($templates)) {
                continue;
            }

            // Default: أول template مذكور في config
            $defaultTemplateKey = array_key_first($templates);
            $selectedTemplate   = $defaultTemplateKey;

            if (isset($rows[$sectionKey])) {
                $group = $rows[$sectionKey];

                // لو فيه فرع معين → نحاول نلاقي صف بـ branch_id = الفرع
                $record = null;

                if ($branch) {
                    $record = $group->firstWhere('branch_id', $branch->id);
                }

                // لو مالقيناش، نشوف الصف العام للمطعم (branch_id = null)
                if (!$record) {
                    $record = $group->firstWhere('branch_id', null);
                }

                // لو لسه، ناخد أول صف موجود
                if (!$record) {
                    $record = $group->first();
                }

                if ($record && isset($templates[$record->template_key])) {
                    $selectedTemplate = $record->template_key;
                }
            }

            $styles[$sectionKey] = $selectedTemplate;
        }

        return $styles;
    }

    /**
     * رجّع كل التمبلتس المتاحة من الـ config علشان تستخدمها
     * في صفحة الإعدادات (Menu Style).
     */
    public function getTemplatesConfig(): array
    {
        $config = config('menu_templates', []);

        return is_array($config) ? $config : [];
    }
}
