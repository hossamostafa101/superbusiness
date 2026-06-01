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

'cashier_id',
'shift_id',
'paid_at',
'pos_notes',

'customer_account_id',
'customer_address_id',





'delivery_zone_id',
'delivery_courier_id',
'delivery_fee',
'delivery_fee_included_in_total',
'show_delivery_fee_on_receipt',
'delivery_fee_payment_target',
'delivery_status',
'delivery_courier_name',
'delivery_courier_phone',
'delivery_company_name',
'delivery_address_details',
'delivery_area',
'delivery_building',
'delivery_floor',
'delivery_apartment',
'delivery_landmark',
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
        'paid_at' => 'datetime',

'delivery_fee_included_in_total' => 'boolean',
'show_delivery_fee_on_receipt' => 'boolean',
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







public function payments()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantOrderPayment::class, 'order_id');
}

public function shift()
{
    return $this->belongsTo(\App\Models\RestaurantMenu\RestaurantPosShift::class, 'shift_id');
}

public function cashier()
{
    return $this->belongsTo(\App\Models\RestaurantMenu\RestaurantStaff::class, 'cashier_id');
}






public function customerAccount()
{
    return $this->belongsTo(\App\Models\WorkspaceCustomerAccount::class, 'customer_account_id');
}

public function customerAddress()
{
    return $this->belongsTo(\App\Models\WorkspaceCustomerAddress::class, 'customer_address_id');
}













public function deliveryZone()
{
    return $this->belongsTo(\App\Models\RestaurantMenu\RestaurantDeliveryZone::class, 'delivery_zone_id');
}

public function deliveryCourier()
{
    return $this->belongsTo(\App\Models\RestaurantMenu\RestaurantDeliveryCourier::class, 'delivery_courier_id');
}











public function isDeliveryOrder(): bool
{
    return $this->order_type === 'delivery';
}

public function shouldShowDeliveryFeeOnReceipt(): bool
{
    return $this->isDeliveryOrder()
        && (float) $this->delivery_fee > 0
        && (bool) $this->show_delivery_fee_on_receipt;
}

public function deliveryStatusLabel(): string
{
    return match ($this->delivery_status) {
        'not_assigned' => 'لم يتم التعيين',
        'assigned' => 'تم التعيين',
        'picked_up' => 'تم الاستلام من المطعم',
        'on_the_way' => 'في الطريق',
        'delivered' => 'تم التسليم',
        'failed' => 'فشل التوصيل',
        default => $this->delivery_status ?: '—',
    };
}

public function deliveryFeeTargetLabel(): string
{
    return match ($this->delivery_fee_payment_target) {
        'restaurant' => 'المطعم',
        'courier' => 'الدليفري',
        'external_company' => 'شركة خارجية',
        default => $this->delivery_fee_payment_target ?: '—',
    };
}



public function events()
{
    return $this->hasMany(\App\Models\RestaurantMenu\RestaurantOrderEvent::class, 'order_id');
}







}