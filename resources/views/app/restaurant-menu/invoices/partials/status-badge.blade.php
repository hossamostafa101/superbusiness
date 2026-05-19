{{-- resources/views/app/restaurant-menu/invoices/partials/status-badge.blade.php --}}
<span class="badge {{ $invoice->statusBadgeClass() }}">
    {{ $invoice->statusLabel() }}
</span>

@if($invoice->status === 'open' && $invoice->expires_at)
    @if(now()->greaterThanOrEqualTo($invoice->expires_at))
        <span class="badge bg-warning text-dark ms-1">
            انتهت المدة
        </span>
    @else
        <span class="badge bg-light text-dark border ms-1">
            تنتهي {{ $invoice->expires_at->diffForHumans() }}
        </span>
    @endif
@endif