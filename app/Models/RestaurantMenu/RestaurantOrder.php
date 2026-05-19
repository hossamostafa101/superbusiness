<?php

namespace App\Models\RestaurantMenu;

use App\Models\BusinessRequest;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class RestaurantOrder extends Model
{
    protected $table = 'restaurant_orders';

    protected $fillable = [
        'workspace_id',
        'branch_id',
        'invoice_id',
        'order_number',

        'customer_name',
        'customer_phone',
        'customer_email',

        'order_type',
        'table_id',
        'table_number',

        'delivery_address',
        'notes',

        'subtotal',
        'discount_total',
        'delivery_fee',
        'tax_total',
        'total',
        'currency',

        'status',
        'payment_status',
        'payment_method',

        'source',
        'metadata',

        'accepted_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'array',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(RestaurantBranch::class, 'branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RestaurantOrderItem::class, 'order_id');
    }

    public function requestRecord(): MorphOne
    {
        return $this->morphOne(BusinessRequest::class, 'reference', 'reference_type', 'reference_id');
    }

    public function customerDisplayName(): string
    {
        return $this->customer_name ?: 'عميل';
    }

    public function customerPhoneForWhatsapp(): ?string
    {
        if (! $this->customer_phone) {
            return null;
        }

        return preg_replace('/\D+/', '', $this->customer_phone);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new' => 'جديد',
            'accepted' => 'مقبول',
            'preparing' => 'قيد التحضير',
            'ready' => 'جاهز',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function orderTypeLabel(): string
    {
        return match ($this->order_type) {
            'dine_in' => 'داخل المكان',
            'takeaway' => 'تيك أواي',
            'delivery' => 'دليفري',
            default => $this->order_type,
        };
    }




    public function whatsappUrl(string $type = 'received'): ?string
{
    $phone = $this->customerPhoneForWhatsapp();

    if (! $phone) {
        return null;
    }

    $message = match ($type) {
        'accepted' => "مرحبًا {$this->customerDisplayName()}، تم قبول طلبك رقم {$this->order_number} وجاري تجهيزه.",
        'ready' => "مرحبًا {$this->customerDisplayName()}، طلبك رقم {$this->order_number} جاهز.",
        'completed' => "مرحبًا {$this->customerDisplayName()}، تم إكمال طلبك رقم {$this->order_number}. شكرًا لاختيارك لنا.",
        'cancelled' => "مرحبًا {$this->customerDisplayName()}، نأسف لإبلاغك أنه تم إلغاء طلبك رقم {$this->order_number}.",
        default => "مرحبًا {$this->customerDisplayName()}، تم استلام طلبك رقم {$this->order_number} بقيمة " . number_format((float) $this->total, 2) . " {$this->currency}.",
    };

    return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
}

public function statusBadgeClass(): string
{
    return match ($this->status) {
        'new' => 'bg-warning text-dark',
        'accepted' => 'bg-primary',
        'preparing' => 'bg-info text-dark',
        'ready' => 'bg-success',
        'completed' => 'bg-dark',
        'cancelled' => 'bg-danger',
        default => 'bg-secondary',
    };
}

public function orderTypeBadgeClass(): string
{
    return match ($this->order_type) {
        'dine_in' => 'bg-primary',
        'takeaway' => 'bg-secondary',
        'delivery' => 'bg-success',
        default => 'bg-secondary',
    };
}









public function table(): BelongsTo
{
    return $this->belongsTo(RestaurantTable::class, 'table_id');
}








public function invoice(): BelongsTo
{
    return $this->belongsTo(RestaurantInvoice::class, 'invoice_id');
}
}