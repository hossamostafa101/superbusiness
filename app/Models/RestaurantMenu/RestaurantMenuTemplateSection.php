<?php

namespace App\Models\RestaurantMenu;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenuTemplateSection extends Model
{
    protected $table = 'restaurant_menu_template_sections';

    protected $fillable = [
        'section_type',
        'name',
        'key',
        'description',
        'preview_image',
        'config',
        'is_premium',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'config' => 'array',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];



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