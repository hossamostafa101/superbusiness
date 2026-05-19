<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantMenuThemeAssignment extends Model
{
    protected $table = 'restaurant_menu_theme_assignments';

    protected $fillable = [
        'workspace_id',
        'mode',
        'template_id',

        'hero_section_id',
        'branch_switch_section_id',
        'category_tabs_section_id',
        'items_section_id',
        'item_modal_section_id',
        'cart_section_id',
        'invoice_section_id',
        'footer_section_id',

        'colors',
        'typography',
        'custom_css',
    ];

    protected $casts = [
        'colors' => 'array',
        'typography' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplate::class, 'template_id');
    }

    public function heroSection(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplateSection::class, 'hero_section_id');
    }

    public function branchSwitchSection(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplateSection::class, 'branch_switch_section_id');
    }

    public function categoryTabsSection(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplateSection::class, 'category_tabs_section_id');
    }

    public function itemsSection(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplateSection::class, 'items_section_id');
    }

    public function itemModalSection(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplateSection::class, 'item_modal_section_id');
    }

    public function cartSection(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplateSection::class, 'cart_section_id');
    }

    public function invoiceSection(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplateSection::class, 'invoice_section_id');
    }

    public function footerSection(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuTemplateSection::class, 'footer_section_id');
    }
}