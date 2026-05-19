<?php

namespace App\Models\RestaurantMenu;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantMenuTemplate extends Model
{
    protected $table = 'restaurant_menu_templates';

    protected $fillable = [
        'name',
        'key',
        'description',
        'preview_image',
        'layout_config',
        'is_premium',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(RestaurantMenuThemeAssignment::class, 'template_id');
    }


    public function previewImageUrl(): ?string
{
    if (! $this->preview_image) {
        return null;
    }

    if (str_starts_with($this->preview_image, 'http://') || str_starts_with($this->preview_image, 'https://')) {
        return $this->preview_image;
    }

    if (str_starts_with($this->preview_image, 'images/')) {
        return asset($this->preview_image);
    }

    return asset('storage/' . $this->preview_image);
}
}