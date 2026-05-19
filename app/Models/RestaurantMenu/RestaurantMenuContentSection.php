<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantMenuContentSection extends Model
{
    protected $table = 'restaurant_menu_content_sections';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'type',
        'title',
        'subtitle',
        'slug',
        'background_type',
        'background_color',
        'background_gradient_from',
        'background_gradient_to',
        'text_color',
        'button_color',
        'layout',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'settings' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function sectionItems(): HasMany
    {
        return $this->hasMany(RestaurantMenuContentSectionItem::class, 'section_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(RestaurantMenuOffer::class, 'section_id');
    }

    public function activeSectionItems(): HasMany
    {
        return $this->sectionItems()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function activeOffers(): HasMany
    {
        return $this->offers()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function cssBackground(): string
    {
        if ($this->background_type === 'gradient' && $this->background_gradient_from && $this->background_gradient_to) {
            return "linear-gradient(135deg, {$this->background_gradient_from}, {$this->background_gradient_to})";
        }

        return $this->background_color ?: '#ffffff';
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'featured_items' => 'أصناف مميزة',
            'item_collection' => 'مجموعة أصناف',
            'offers_slider' => 'سلايدر عروض',
            default => $this->type,
        };
    }
}