<?php

namespace App\Models\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantMenuOffer extends Model
{
    protected $table = 'restaurant_menu_offers';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'section_id',
        'item_id',
        'title',
        'subtitle',
        'description',
        'image',
        'badge_text',
        'old_price',
        'new_price',
        'currency',
        'button_text',
        'button_url',
        'background_color',
        'text_color',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
        'metadata',

        'is_orderable',
'order_mode',
'button_action',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'metadata' => 'array',

        'is_orderable' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuContentSection::class, 'section_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'item_id');
    }



    public function offerItems()
{
    return $this->hasMany(RestaurantMenuOfferItem::class, 'offer_id')
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function activeOfferItems()
{
    return $this->hasMany(RestaurantMenuOfferItem::class, 'offer_id')
        ->with('item')
        ->orderBy('sort_order')
        ->orderBy('id');
}





    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        return asset('storage/' . $this->image);
    }
    
}