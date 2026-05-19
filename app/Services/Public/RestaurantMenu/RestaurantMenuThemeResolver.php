<?php

namespace App\Services\Public\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantMenuTemplate;
use App\Models\RestaurantMenu\RestaurantMenuTemplateSection;
use App\Models\RestaurantMenu\RestaurantMenuThemeAssignment;
use App\Models\Workspace;
use Illuminate\Support\Facades\View;

class RestaurantMenuThemeResolver
{
    public function resolve(Workspace $workspace): array
    {
        if (request()->boolean('theme_preview')) {
    return $this->resolveLivePreview($workspace);
}

        if (request()->filled('preview_template')) {
            $template = RestaurantMenuTemplate::query()
                ->where('key', request()->input('preview_template'))
                ->where('is_active', true)
                ->first();

            if ($template) {
                $assignment = $workspace->restaurantMenuThemeAssignment()->first()
                    ?? $this->createDefaultAssignment($workspace);

                $assignment->setRelation('template', $template);
                $assignment->mode = 'template';

                return $this->resolveTemplate($assignment);
            }
        }

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

        if (! $assignment) {
            $assignment = $this->createDefaultAssignment($workspace);
        }

        if ($assignment->mode === 'custom') {
            return $this->resolveCustom($assignment);
        }

        return $this->resolveTemplate($assignment);
    }

    private function createDefaultAssignment(Workspace $workspace): RestaurantMenuThemeAssignment
    {
        $template = RestaurantMenuTemplate::query()
            ->where('key', 'classic_cafe')
            ->where('is_active', true)
            ->first();

        return RestaurantMenuThemeAssignment::create([
            'workspace_id' => $workspace->id,
            'mode' => 'template',
            'template_id' => $template?->id,

            'colors' => [
    'theme_color' => '#2a120d',
    'button_color' => '#8caf50',
    'background_color' => '#f3eee6',
    'text_color' => '#1f1713',
],

            'typography' => [
                'font_family' => 'system',
            ],
        ]);
    }

    private function resolveTemplate(RestaurantMenuThemeAssignment $assignment): array
    {
        $template = $assignment->template;

        $layout = $template?->layout_config ?: [];

        $sections = [
            'hero' => $this->sectionByKey($layout['hero'] ?? 'hero_classic', 'hero'),
            'branch_switch' => $this->sectionByKey($layout['branch_switch'] ?? 'branch_pills', 'branch_switch'),
            'category_tabs' => $this->sectionByKey($layout['category_tabs'] ?? 'tabs_pills', 'category_tabs'),
            'items' => $this->sectionByKey($layout['items'] ?? 'items_list_compact', 'items'),
            'item_modal' => $this->sectionByKey($layout['item_modal'] ?? 'modal_centered', 'item_modal'),
            'cart' => $this->sectionByKey($layout['cart'] ?? 'cart_floating', 'cart'),
            'invoice' => $this->sectionByKey($layout['invoice'] ?? 'invoice_alert', 'invoice'),
            'footer' => $this->sectionByKey($layout['footer'] ?? 'footer_simple', 'footer'),
        ];

        return $this->buildThemePayload($assignment, $sections);
    }

    private function resolveCustom(RestaurantMenuThemeAssignment $assignment): array
    {
        $sections = [
            'hero' => $assignment->heroSection,
            'branch_switch' => $assignment->branchSwitchSection,
            'category_tabs' => $assignment->categoryTabsSection,
            'items' => $assignment->itemsSection,
            'item_modal' => $assignment->itemModalSection,
            'cart' => $assignment->cartSection,
            'invoice' => $assignment->invoiceSection,
            'footer' => $assignment->footerSection,
        ];

        return $this->buildThemePayload($assignment, $sections);
    }

    private function sectionByKey(string $key, string $sectionType): ?RestaurantMenuTemplateSection
    {
        return RestaurantMenuTemplateSection::query()
            ->where('key', $key)
            ->where('section_type', $sectionType)
            ->where('is_active', true)
            ->first();
    }

    private function buildThemePayload(RestaurantMenuThemeAssignment $assignment, array $sections): array
    {
        $sectionKeys = [
            'hero' => $sections['hero']?->key ?? 'hero_classic',
            'branch_switch' => $sections['branch_switch']?->key ?? 'branch_pills',
            'category_tabs' => $sections['category_tabs']?->key ?? 'tabs_pills',
            'items' => $sections['items']?->key ?? 'items_list_compact',
            'item_modal' => $sections['item_modal']?->key ?? 'modal_centered',
            'cart' => $sections['cart']?->key ?? 'cart_floating',
            'invoice' => $sections['invoice']?->key ?? 'invoice_alert',
            'footer' => $sections['footer']?->key ?? 'footer_simple',
        ];

        return [
            'mode' => $assignment->mode,
            'template' => $assignment->template,

            'colors' => array_merge([
                'theme_color' => '#111827',
                'button_color' => '#2563eb',
                'background_color' => '#f6f7fb',
                'text_color' => '#111827',
            ], $assignment->colors ?? []),

            'typography' => array_merge([
                'font_family' => 'system',
            ], $assignment->typography ?? []),

            'custom_css' => $assignment->custom_css,

            'sections' => $sectionKeys,

            'section_models' => $sections,

            'views' => [
                'hero' => $this->viewFor('hero', $sectionKeys['hero'], $sections['hero']),
                'branch_switch' => $this->viewFor('branch_switch', $sectionKeys['branch_switch'], $sections['branch_switch']),
                'category_tabs' => $this->viewFor('category_tabs', $sectionKeys['category_tabs'], $sections['category_tabs']),
                'items' => $this->viewFor('items', $sectionKeys['items'], $sections['items']),
                'item_modal' => $this->viewFor('item_modal', $sectionKeys['item_modal'], $sections['item_modal']),
                'cart' => $this->viewFor('cart', $sectionKeys['cart'], $sections['cart']),
                'invoice' => $this->viewFor('invoice', $sectionKeys['invoice'], $sections['invoice']),
                'footer' => $this->viewFor('footer', $sectionKeys['footer'], $sections['footer']),
            ],
        ];
    }

    private function viewFor(string $sectionType, string $key, ?RestaurantMenuTemplateSection $section = null): string
    {
        $configuredView = $section?->config['view'] ?? null;

        if ($configuredView && is_string($configuredView) && View::exists($configuredView)) {
            return $configuredView;
        }

        $map = [
            'hero' => [
                'hero_classic' => 'public.restaurant-menu.templates.sections.heroes.classic',
                'hero_modern_gradient' => 'public.restaurant-menu.templates.sections.heroes.modern-gradient',
                'hero_luxury' => 'public.restaurant-menu.templates.sections.heroes.luxury',
            ],

            'branch_switch' => [
                'branch_pills' => 'public.restaurant-menu.templates.sections.branch-switch.pills',
                'branch_cards' => 'public.restaurant-menu.templates.sections.branch-switch.cards',
                'branch_minimal' => 'public.restaurant-menu.templates.sections.branch-switch.minimal',
            ],

            'category_tabs' => [
                'tabs_pills' => 'public.restaurant-menu.templates.sections.category-tabs.pills',
                'tabs_sticky' => 'public.restaurant-menu.templates.sections.category-tabs.sticky',
                'tabs_underline' => 'public.restaurant-menu.templates.sections.category-tabs.underline',
            ],

            'items' => [
                'items_list_compact' => 'public.restaurant-menu.templates.sections.items.list-compact',
                'items_cards_large' => 'public.restaurant-menu.templates.sections.items.cards-large',
                'items_elegant' => 'public.restaurant-menu.templates.sections.items.elegant',
            ],

            'item_modal' => [
                'modal_centered' => 'public.restaurant-menu.templates.sections.item-modal.centered',
                'modal_bottom_sheet' => 'public.restaurant-menu.templates.sections.item-modal.bottom-sheet',
                'modal_luxury' => 'public.restaurant-menu.templates.sections.item-modal.luxury',
            ],

            'cart' => [
                'cart_floating' => 'public.restaurant-menu.templates.sections.cart.floating',
                'cart_bottom_bar' => 'public.restaurant-menu.templates.sections.cart.bottom-bar',
            ],

            'invoice' => [
                'invoice_alert' => 'public.restaurant-menu.templates.sections.invoice.alert',
                'invoice_card' => 'public.restaurant-menu.templates.sections.invoice.card',
                'invoice_minimal' => 'public.restaurant-menu.templates.sections.invoice.minimal',
            ],

            'footer' => [
                'footer_simple' => 'public.restaurant-menu.templates.sections.footer.simple',
                'footer_brand' => 'public.restaurant-menu.templates.sections.footer.brand',
                'footer_luxury' => 'public.restaurant-menu.templates.sections.footer.luxury',
            ],
        ];

        $view = $map[$sectionType][$key] ?? $this->fallbackView($sectionType);

        if (View::exists($view)) {
            return $view;
        }

        return $this->fallbackView($sectionType);
    }

    private function fallbackView(string $sectionType): string
    {
        return match ($sectionType) {
            'hero' => 'public.restaurant-menu.templates.sections.heroes.classic',
            'branch_switch' => 'public.restaurant-menu.templates.sections.branch-switch.pills',
            'category_tabs' => 'public.restaurant-menu.templates.sections.category-tabs.pills',
            'items' => 'public.restaurant-menu.templates.sections.items.list-compact',
            'item_modal' => 'public.restaurant-menu.templates.sections.item-modal.centered',
            'cart' => 'public.restaurant-menu.templates.sections.cart.floating',
            'invoice' => 'public.restaurant-menu.templates.sections.invoice.alert',
            'footer' => 'public.restaurant-menu.templates.sections.footer.simple',
            default => 'public.restaurant-menu.templates.sections.footer.simple',
        };
    }














    private function resolveLivePreview(Workspace $workspace): array
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

    if (! $assignment) {
        $assignment = $this->createDefaultAssignment($workspace);
    }

    $mode = request()->input('preview_mode', $assignment->mode ?: 'template');

    $assignment->mode = in_array($mode, ['template', 'custom'], true)
        ? $mode
        : 'template';

    $assignment->colors = $this->previewColors($assignment->colors ?? []);
    $assignment->typography = $this->previewTypography($assignment->typography ?? []);

    if ($assignment->mode === 'template') {
        $templateId = request()->integer('preview_template_id');

        if ($templateId) {
            $template = RestaurantMenuTemplate::query()
                ->where('is_active', true)
                ->find($templateId);

            if ($template) {
                $assignment->setRelation('template', $template);
                $assignment->template_id = $template->id;
            }
        }

        return $this->resolveTemplate($assignment);
    }

    $sections = [
        'hero' => $this->sectionById(request()->integer('preview_hero_section_id'), 'hero')
            ?: $assignment->heroSection,

        'branch_switch' => $this->sectionById(request()->integer('preview_branch_switch_section_id'), 'branch_switch')
            ?: $assignment->branchSwitchSection,

        'category_tabs' => $this->sectionById(request()->integer('preview_category_tabs_section_id'), 'category_tabs')
            ?: $assignment->categoryTabsSection,

        'items' => $this->sectionById(request()->integer('preview_items_section_id'), 'items')
            ?: $assignment->itemsSection,

        'item_modal' => $this->sectionById(request()->integer('preview_item_modal_section_id'), 'item_modal')
            ?: $assignment->itemModalSection,

        'cart' => $this->sectionById(request()->integer('preview_cart_section_id'), 'cart')
            ?: $assignment->cartSection,

        'invoice' => $this->sectionById(request()->integer('preview_invoice_section_id'), 'invoice')
            ?: $assignment->invoiceSection,

        'footer' => $this->sectionById(request()->integer('preview_footer_section_id'), 'footer')
            ?: $assignment->footerSection,
    ];

    return $this->buildThemePayload($assignment, $sections);
}

private function sectionById(?int $id, string $sectionType): ?RestaurantMenuTemplateSection
{
    if (! $id) {
        return null;
    }

    return RestaurantMenuTemplateSection::query()
        ->where('id', $id)
        ->where('section_type', $sectionType)
        ->where('is_active', true)
        ->first();
}

private function previewColors(array $currentColors): array
{
    return array_merge([
        'theme_color' => '#111827',
        'button_color' => '#2563eb',
        'background_color' => '#f6f7fb',
        'text_color' => '#111827',
    ], $currentColors, [
        'theme_color' => request()->input('preview_theme_color', $currentColors['theme_color'] ?? '#111827'),
        'button_color' => request()->input('preview_button_color', $currentColors['button_color'] ?? '#2563eb'),
        'background_color' => request()->input('preview_background_color', $currentColors['background_color'] ?? '#f6f7fb'),
        'text_color' => request()->input('preview_text_color', $currentColors['text_color'] ?? '#111827'),
    ]);
}

private function previewTypography(array $currentTypography): array
{
    return array_merge([
        'font_family' => 'system',
    ], $currentTypography, [
        'font_family' => request()->input('preview_font_family', $currentTypography['font_family'] ?? 'system'),
    ]);
}
}