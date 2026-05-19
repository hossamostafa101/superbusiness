<?php

namespace App\Services\Public\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantMenuTemplate;
use App\Models\RestaurantMenu\RestaurantMenuTemplateSection;
use App\Models\RestaurantMenu\RestaurantMenuThemeAssignment;
use App\Models\Workspace;

class RestaurantMenuThemeResolver
{
    public function resolve(Workspace $workspace): array
    {

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

    private function resolveTemplate(RestaurantMenuThemeAssignment $assignment): array
    {
        $template = $assignment->template;

        $layout = $template?->layout_config ?: [];

        return $this->buildThemePayload(
            assignment: $assignment,
            sections: [
                'hero' => $layout['hero'] ?? 'hero_classic',
                'branch_switch' => $layout['branch_switch'] ?? 'branch_pills',
                'category_tabs' => $layout['category_tabs'] ?? 'tabs_pills',
                'items' => $layout['items'] ?? 'items_list_compact',
                'item_modal' => $layout['item_modal'] ?? 'modal_centered',
                'cart' => $layout['cart'] ?? 'cart_floating',
                'invoice' => $layout['invoice'] ?? 'invoice_alert',
                'footer' => $layout['footer'] ?? 'footer_simple',
            ]
        );
    }

    private function resolveCustom(RestaurantMenuThemeAssignment $assignment): array
    {
        return $this->buildThemePayload(
            assignment: $assignment,
            sections: [
                'hero' => $assignment->heroSection?->key ?? 'hero_classic',
                'branch_switch' => $assignment->branchSwitchSection?->key ?? 'branch_pills',
                'category_tabs' => $assignment->categoryTabsSection?->key ?? 'tabs_pills',
                'items' => $assignment->itemsSection?->key ?? 'items_list_compact',
                'item_modal' => $assignment->itemModalSection?->key ?? 'modal_centered',
                'cart' => $assignment->cartSection?->key ?? 'cart_floating',
                'invoice' => $assignment->invoiceSection?->key ?? 'invoice_alert',
                'footer' => $assignment->footerSection?->key ?? 'footer_simple',
            ]
        );
    }

    private function buildThemePayload(RestaurantMenuThemeAssignment $assignment, array $sections): array
    {
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

            'sections' => $sections,

            'views' => [
                'hero' => $this->viewFor('hero', $sections['hero']),
                'branch_switch' => $this->viewFor('branch_switch', $sections['branch_switch']),
                'category_tabs' => $this->viewFor('category_tabs', $sections['category_tabs']),
                'items' => $this->viewFor('items', $sections['items']),
                'item_modal' => $this->viewFor('item_modal', $sections['item_modal']),
                'cart' => $this->viewFor('cart', $sections['cart']),
                'invoice' => $this->viewFor('invoice', $sections['invoice']),
                'footer' => $this->viewFor('footer', $sections['footer']),
            ],
        ];
    }

    private function viewFor(string $sectionType, string $key): string
    {
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

        return $map[$sectionType][$key]
            ?? $this->fallbackView($sectionType);
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
}