<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuSectionStyle;
use App\Models\Restaurant;
use App\Models\Branch;
use App\Services\MenuStyleService;
use Illuminate\Http\Request;

class MenuStyleController extends Controller
{
    protected MenuStyleService $styleService;

    public function __construct(MenuStyleService $styleService)
    {
        $this->styleService = $styleService;
    }

    /**
     * صفحة تعديل تصميم المنيو (اختيار تمبلت لكل سكشن + معاينة).
     */
    public function edit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // لو اليوزر عنده مطعم واحد فقط:
        /** @var Restaurant $restaurant */
        $restaurant = $user->restaurants()->firstOrFail();

        /** @var Branch|null $branch */
        $branch = $restaurant->branches()
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        $templatesConfig = $this->styleService->getTemplatesConfig();
        $currentStyles   = $this->styleService->getStylesFor($restaurant, $branch);

        return view('restaurant.sections.menu_style.edit', [
            'restaurant'      => $restaurant,
            'branch'          => $branch,
            'templatesConfig' => $templatesConfig,
            'currentStyles'   => $currentStyles,
        ]);
    }

    /**
     * حفظ اختيارات التمبلت لكل سكشن.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var Restaurant $restaurant */
        $restaurant = $user->restaurants()->firstOrFail();

        /** @var Branch|null $branch */
        $branch = $restaurant->branches()
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        $data = $request->validate([
            'sections'                => ['required', 'array'],
            'sections.*.template_key' => ['required', 'string'],
        ]);

        foreach ($data['sections'] as $sectionKey => $sectionData) {
            $templateKey = $sectionData['template_key'] ?? null;
            if (!$templateKey) {
                continue;
            }

            MenuSectionStyle::updateOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'branch_id'     => $branch?->id, // لو عايزة per-branch
                    'section_key'   => $sectionKey,
                ],
                [
                    'template_key'  => $templateKey,
                ]
            );
        }

        return back()->with('success', 'تم حفظ إعدادات تصميم المنيو بنجاح.');
    }
}
