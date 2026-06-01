<?php

namespace Modules\Affiliate\Models;

use App\Models\Specification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateResource extends Model
{
    protected $table = 'affiliate_resources';

    protected $fillable = [
        'specification_id',
        'title',
        'description',
        'type',
        'content',
        'url',
        'file_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function specification(): BelongsTo
    {
        return $this->belongsTo(Specification::class, 'specification_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'text' => 'نص',
            'link' => 'رابط',
            'video' => 'فيديو',
            'image' => 'صورة',
            'pdf' => 'PDF',
            'demo' => 'ديمو',
            'whatsapp_script' => 'نص واتساب',
            'other' => 'أخرى',
            default => 'مورد',
        };
    }
}