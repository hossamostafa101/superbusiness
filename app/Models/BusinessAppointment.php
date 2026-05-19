<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessAppointment extends Model
{
    protected $fillable = [
        'workspace_id',
        'customer_id',
        'service_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'source',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'metadata' => 'array',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(BusinessCustomer::class, 'customer_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(BusinessService::class, 'service_id');
    }












    public function contactPhone(): ?string
{
    return $this->customer?->phone ?: $this->customer_phone;
}

public function contactName(): string
{
    return $this->customer?->name ?: $this->customer_name ?: 'عميلنا';
}

public function serviceName(): string
{
    return $this->service?->name ?: 'الخدمة';
}

public function whatsappUrl(string $type = 'confirm'): ?string
{
    $phone = $this->contactPhone();

    if (! $phone) {
        return null;
    }

    $phone = preg_replace('/\D+/', '', $phone);

    $date = $this->appointment_date?->format('Y-m-d') ?: '';
    $time = $this->start_time ? substr($this->start_time, 0, 5) : '';

    $message = match ($type) {
        'reminder' => "مرحبًا {$this->contactName()}، نذكرك بموعدك يوم {$date} الساعة {$time} لخدمة {$this->serviceName()}.",
        'cancel' => "مرحبًا {$this->contactName()}، نأسف لإبلاغك أنه تم إلغاء موعدك يوم {$date} الساعة {$time}. يمكنك التواصل معنا لترتيب موعد آخر.",
        default => "مرحبًا {$this->contactName()}، تم تأكيد موعدك يوم {$date} الساعة {$time} لخدمة {$this->serviceName()}.",
    };

    return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
}
}