@extends('app.layouts.app')

@section('title', 'طلبات المطعم')
@section('page_title', 'Orders Master')
@section('page_description', 'إدارة الطلبات وطلبات الطاولات من مكان واحد.')

@section('content')
<div class="orders-master-page">
    <div class="orders-toolbar">
        <div class="orders-tabs">
            <a href="{{ route('app.restaurant-menu.orders.index', ['workspace' => $workspace, 'status' => 'active']) }}"
               class="orders-tab {{ $status === 'active' ? 'active' : '' }}">
                <i class="bi bi-activity"></i>
                Active
                <span data-count-key="active">{{ $counts['active'] ?? 0 }}</span>
            </a>

            <a href="{{ route('app.restaurant-menu.orders.index', ['workspace' => $workspace, 'status' => 'new']) }}"
               class="orders-tab {{ $status === 'new' ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                New
                <span data-count-key="new">{{ $counts['new'] ?? 0 }}</span>
            </a>

            <a href="{{ route('app.restaurant-menu.orders.index', ['workspace' => $workspace, 'status' => 'preparing']) }}"
               class="orders-tab {{ $status === 'preparing' ? 'active' : '' }}">
                <i class="bi bi-fire"></i>
                Kitchen
               <span data-count-key="preparing">{{ $counts['preparing'] ?? 0 }}</span>
            </a>

            <a href="{{ route('app.restaurant-menu.orders.index', ['workspace' => $workspace, 'status' => 'ready']) }}"
               class="orders-tab {{ $status === 'ready' ? 'active' : '' }}">
                <i class="bi bi-check-circle"></i>
               <span data-count-key="ready">{{ $counts['ready'] ?? 0 }}</span>
            </a>

            <span class="orders-tab service-tab">
                <i class="bi bi-bell-fill"></i>
                Requests
                <span data-count-key="service_requests">{{ $counts['service_requests'] ?? 0 }}</span>
            </span>
        </div>
    </div>

    <div class="live-status-bar">
    <span class="live-dot"></span>
    <span>
        Live
    </span>

    <small class="text-muted">
        آخر تحديث:
        <span id="ordersLastUpdate">--:--:--</span>
    </small>
</div>

    <div class="orders-grid" id="ordersGrid">
    @include('app.restaurant-menu.orders.partials.cards')
</div>
</div>

<style>
.orders-master-page {
    --om-red: #c91f26;
    --om-blue: #243f63;
    --om-border: #e7e9ee;
    --om-text: #273142;
    --om-muted: #7b8494;
}

.orders-toolbar {
    margin-bottom: 22px;
    overflow-x: auto;
    padding-bottom: 4px;
}

.orders-tabs {
    display: flex;
    gap: 14px;
    min-width: max-content;
}

.orders-tab {
    min-height: 48px;
    padding: 0 18px;
    border-radius: 999px;
    background: #fff;
    color: var(--om-text);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    font-weight: 800;
    box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
}

.orders-tab span {
    min-width: 24px;
    height: 24px;
    border-radius: 999px;
    display: inline-grid;
    place-items: center;
    background: rgba(15, 23, 42, .06);
    font-size: 12px;
}

.orders-tab.active {
    background: var(--om-red);
    color: #fff;
}

.orders-tab.active span {
    background: rgba(255,255,255,.18);
}

.service-tab {
    background: #fff7ed;
    color: #9a3412;
}

.orders-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.operation-card {
    background: #fff;
    border: 1px solid var(--om-border);
    border-radius: 24px;
    padding: 18px;
    box-shadow: 0 16px 35px rgba(15, 23, 42, .06);
}

.operation-card-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.table-chip {
    background: #f4f1f1;
    border-radius: 10px;
    padding: 6px 12px;
    font-weight: 900;
    color: #3a4354;
}

.order-number {
    font-size: 18px;
    color: var(--om-text);
}

.time-text {
    margin-inline-start: auto;
    color: var(--om-muted);
    font-size: 14px;
    white-space: nowrap;
}

.operation-tags {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 14px;
}

.tag {
    border-radius: 12px;
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.tag-blue {
    background: #e8f0ff;
    color: #2764c7;
}

.tag-green {
    background: #dffbea;
    color: #047857;
}

.tag-purple {
    background: #f1e4ff;
    color: #7e22ce;
}

.customer-row {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--om-text);
    font-size: 16px;
    margin-bottom: 10px;
}

.order-total {
    margin-inline-start: auto;
    font-weight: 900;
}

.table-note {
    color: var(--om-muted);
    font-size: 13px;
    margin-bottom: 10px;
}

.items-list {
    list-style: none;
    padding: 0;
    margin: 0 0 18px;
}

.items-list li {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    color: #596273;
    margin-bottom: 7px;
    font-size: 14px;
}

.items-list li::before {
    content: "•";
    color: #9ca3af;
}

.items-list small {
    color: var(--om-muted);
    white-space: nowrap;
}

.operation-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.operation-actions form {
    margin: 0;
}

.action-btn {
    width: 100%;
    min-height: 46px;
    border: 0;
    border-radius: 13px;
    font-weight: 850;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
}

.action-btn.primary {
    background: var(--om-blue);
    color: #fff;
}

.action-btn.secondary {
    background: #e8ebef;
    color: var(--om-blue);
}

.done-label {
    grid-column: 1 / -1;
    background: #ecfdf5;
    color: #047857;
    border-radius: 14px;
    padding: 12px;
    text-align: center;
    font-weight: 900;
}

.waiter-request {
    background: linear-gradient(135deg, #fff7ed, #fff);
}

.cash-request {
    background: linear-gradient(135deg, #ecfdf5, #fff);
}

.request-icon {
    width: 42px;
    height: 42px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    background: var(--om-red);
    color: #fff;
    font-size: 18px;
}

.cash-request .request-icon {
    background: #047857;
}

.request-table {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 14px;
}

.request-table small {
    display: block;
    color: var(--om-muted);
    margin-top: 2px;
}

.request-message {
    background: rgba(255,255,255,.75);
    border-radius: 16px;
    padding: 14px;
    color: var(--om-text);
    margin-bottom: 16px;
    font-weight: 800;
}

.empty-orders {
    grid-column: 1 / -1;
    background: #fff;
    border-radius: 24px;
    padding: 40px;
    text-align: center;
    color: var(--om-muted);
}

@media (max-width: 1199px) {
    .orders-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    .orders-grid {
        grid-template-columns: 1fr;
    }

    .operation-card {
        border-radius: 20px;
        padding: 15px;
    }

    .orders-tab {
        min-height: 44px;
        padding: 0 14px;
    }
}


















.live-status-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    font-weight: 800;
    color: #243f63;
}

.live-dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: #16a34a;
    box-shadow: 0 0 0 6px rgba(22, 163, 74, .12);
}

.orders-grid.updating {
    opacity: .65;
}



body.has-new-order-flash .orders-master-page {
    animation: newOrderFlash .9s ease;
}

@keyframes newOrderFlash {
    0% {
        box-shadow: inset 0 0 0 9999px rgba(22, 163, 74, .08);
    }

    100% {
        box-shadow: inset 0 0 0 9999px rgba(22, 163, 74, 0);
    }
}

.action-btn.print-btn {
    background: #111827;
    color: #fff;
    text-decoration: none;
}



.operation-actions {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}
</style>
@endsection




















@push('scripts')
<script>
(function () {
    const grid = document.getElementById('ordersGrid');
    const lastUpdateEl = document.getElementById('ordersLastUpdate');

    if (!grid) {
        return;
    }

    const liveUrl = @json(route('app.restaurant-menu.orders.live', ['workspace' => $workspace, 'status' => $status]));
    let isLoading = false;
    let lastEventId = @json($lastEventId ?? null);
    let firstLoad = true;

    function updateCounts(counts) {
        Object.keys(counts || {}).forEach(function (key) {
            document.querySelectorAll('[data-count-key="' + key + '"]').forEach(function (el) {
                el.textContent = counts[key];
            });
        });
    }

    function playNewEventHint(newEventId) {
        if (!newEventId || firstLoad) {
            return;
        }

        if (lastEventId && newEventId !== lastEventId) {
            document.body.classList.add('has-new-order-flash');

            setTimeout(function () {
                document.body.classList.remove('has-new-order-flash');
            }, 900);
        }
    }

    async function refreshOrders() {
        if (isLoading) {
            return;
        }

        isLoading = true;

        try {
            const response = await fetch(liveUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                cache: 'no-store',
            });

            if (!response.ok) {
                throw new Error('Live request failed');
            }

            const data = await response.json();

            playNewEventHint(data.last_event_id);

            grid.innerHTML = data.html;

            updateCounts(data.counts);

            if (lastUpdateEl) {
                lastUpdateEl.textContent = data.server_time;
            }

            lastEventId = data.last_event_id || lastEventId;
            firstLoad = false;
        } catch (error) {
            console.error(error);
        } finally {
            isLoading = false;
        }
    }

    setInterval(refreshOrders, 5000);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            refreshOrders();
        }
    });

    setTimeout(refreshOrders, 1000);
})();
</script>
@endpush