<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantMenuTemplate;
use App\Models\RestaurantMenu\RestaurantMenuTemplateSection;
use App\Models\RestaurantMenu\RestaurantMenuThemeAssignment;
use App\Models\Workspace;
use App\Services\Core\FeatureLimitService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RestaurantMenuThemeService
{
    public function __construct(
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function assignment(Workspace $workspace): RestaurantMenuThemeAssignment
    {
        $assignment = $workspace->restaurantMenuThemeAssignment()
            ->with([
                'template',
                'heroSection',
                'branchSwitchSection',
                'categoryTabsSection',
                'itemsSection',
                'itemModalSection',
                'cartSection',
                'invoiceSection',
                'footerSection',
            ])
            ->first();

        if ($assignment) {
            return $assignment;
        }

        $defaultTemplate = RestaurantMenuTemplate::query()
            ->where('key', 'classic_cafe')
            ->where('is_active', true)
            ->first();

        return RestaurantMenuThemeAssignment::create([
            'workspace_id' => $workspace->id,
            'mode' => 'template',
            'template_id' => $defaultTemplate?->id,
            'colors' => [
                'theme_color' => '#111827',
                'button_color' => '#2563eb',
                'background_color' => '#f6f7fb',
                'text_color' => '#111827',
            ],
            'typography' => [
                'font_family' => 'system',
            ],
        ]);
    }

    public function options(Workspace $workspace): array
    {
        $premiumEnabled = $this->featureLimitService->enabled(
            workspace: $workspace,
            featureKey: 'restaurant_menu_premium_templates_enabled'
        );

        $templates = RestaurantMenuTemplate::query()
            ->where('is_active', true)
            ->when(! $premiumEnabled, function ($query) {
                $query->where('is_premium', false);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $sections = RestaurantMenuTemplateSection::query()
            ->where('is_active', true)
            ->when(! $premiumEnabled, function ($query) {
                $query->where('is_premium', false);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('section_type');

        return [
            'templates' => $templates,
            'sections' => $sections,
            'premiumEnabled' => $premiumEnabled,
        ];
    }

    public function update(Workspace $workspace, array $data): RestaurantMenuThemeAssignment
    {
        return DB::transaction(function () use ($workspace, $data) {
            $assignment = $this->assignment($workspace);

            $mode = $data['mode'];

            if ($mode === 'custom' && ! $this->featureLimitService->enabled($workspace, 'restaurant_menu_custom_sections_enabled')) {
                throw new RuntimeException('تخصيص أقسام المنيو غير متاح في باقتك الحالية.');
            }

            if (! empty($data['custom_css']) && ! $this->featureLimitService->enabled($workspace, 'restaurant_menu_custom_css_enabled')) {
                throw new RuntimeException('CSS المخصص غير متاح في باقتك الحالية.');
            }

            if ($mode === 'template') {
                $template = RestaurantMenuTemplate::query()
                    ->where('is_active', true)
                    ->findOrFail($data['template_id']);

                $this->ensurePremiumAllowed($workspace, $template->is_premium);

                $assignment->update([
                    'mode' => 'template',
                    'template_id' => $template->id,

                    'hero_section_id' => null,
                    'branch_switch_section_id' => null,
                    'category_tabs_section_id' => null,
                    'items_section_id' => null,
                    'item_modal_section_id' => null,
                    'cart_section_id' => null,
                    'invoice_section_id' => null,
                    'footer_section_id' => null,

                    'colors' => $this->colorsPayload($data),
                    'typography' => $this->typographyPayload($data),
                    'custom_css' => $data['custom_css'] ?? null,
                ]);

                return $assignment->refresh();
            }

            $sectionIds = [
                'hero_section_id' => $data['hero_section_id'] ?? null,
                'branch_switch_section_id' => $data['branch_switch_section_id'] ?? null,
                'category_tabs_section_id' => $data['category_tabs_section_id'] ?? null,
                'items_section_id' => $data['items_section_id'] ?? null,
                'item_modal_section_id' => $data['item_modal_section_id'] ?? null,
                'cart_section_id' => $data['cart_section_id'] ?? null,
                'invoice_section_id' => $data['invoice_section_id'] ?? null,
                'footer_section_id' => $data['footer_section_id'] ?? null,
            ];

            $this->validateCustomSections($workspace, $sectionIds);

            $assignment->update(array_merge([
                'mode' => 'custom',
                'template_id' => null,
                'colors' => $this->colorsPayload($data),
                'typography' => $this->typographyPayload($data),
                'custom_css' => $data['custom_css'] ?? null,
            ], $sectionIds));

            return $assignment->refresh();
        });
    }

    private function validateCustomSections(Workspace $workspace, array $sectionIds): void
    {
        $expectedTypes = [
            'hero_section_id' => 'hero',
            'branch_switch_section_id' => 'branch_switch',
            'category_tabs_section_id' => 'category_tabs',
            'items_section_id' => 'items',
            'item_modal_section_id' => 'item_modal',
            'cart_section_id' => 'cart',
            'invoice_section_id' => 'invoice',
            'footer_section_id' => 'footer',
        ];

        foreach ($expectedTypes as $field => $sectionType) {
            $id = $sectionIds[$field] ?? null;

            if (! $id) {
                continue;
            }

            $section = RestaurantMenuTemplateSection::query()
                ->where('is_active', true)
                ->where('section_type', $sectionType)
                ->findOrFail($id);

            $this->ensurePremiumAllowed($workspace, $section->is_premium);
        }
    }

    private function ensurePremiumAllowed(Workspace $workspace, bool $isPremium): void
    {
        if (! $isPremium) {
            return;
        }

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_menu_premium_templates_enabled')) {
            throw new RuntimeException('هذا القالب أو القسم متاح فقط في الباقات الأعلى.');
        }
    }

    private function colorsPayload(array $data): array
    {
        return [
            'theme_color' => $data['theme_color'] ?? '#111827',
            'button_color' => $data['button_color'] ?? '#2563eb',
            'background_color' => $data['background_color'] ?? '#f6f7fb',
            'text_color' => $data['text_color'] ?? '#111827',
        ];
    }

    private function typographyPayload(array $data): array
    {
        return [
            'font_family' => $data['font_family'] ?? 'system',
        ];
    }
}