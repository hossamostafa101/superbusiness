{{-- resources/views/app/restaurant-menu/orders/partials/status-badge.blade.php --}}
<span class="badge {{ $order->statusBadgeClass() }}">
    {{ $order->statusLabel() }}
</span>