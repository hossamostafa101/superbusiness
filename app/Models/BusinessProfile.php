<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProfile extends Model
{
    protected $fillable = [
        'workspace_id',
        'display_name',
        'tagline',
        'description',
        'logo',
        'cover_image',
        'whatsapp_number',
        'phone',
        'email',
        'address',
        'location_url',
        'theme_color',
        'button_color',
        'text_color',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }
}