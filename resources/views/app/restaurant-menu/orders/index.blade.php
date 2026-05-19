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
                <span>{{ $counts['active'] }}</span>
            </a>

            <a href="{{ route('app.restaurant-menu.orders.index', ['workspace' => $workspace, 'status' => 'new']) }}"
               class="orders-tab {{ $status === 'new' ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                New
                <span>{{ $counts['new'] }}</span>
            </a>

            <a href="{{ route('app.restaurant-menu.orders.index', ['workspace' => $workspace, 'status' => 'preparing']) }}"
               class="orders-tab {{ $status === 'preparing' ? 'active' : '' }}">
                <i class="bi bi-fire"></i>
                Kitchen
                <span>{{ $counts['preparing'] }}</span>
            </a>

            <a href="{{ route('app.restaurant-menu.orders.index', ['workspace' => $workspace, 'status' => 'ready']) }}"
               class="orders-tab {{ $status === 'ready' ? 'active' : '' }}">
                <i class="bi bi-check-circle"></i>
                Ready
                <span>{{ $counts['ready'] }}</span>
            </a>

            <span class="orders-tab service-tab">
                <i class="bi bi-bell-fill"></i>
                Requests
                <span>{{ $counts['service_requests'] }}</span>
            </span>
        </div>
    </div>

    <div class="orders-grid">
        @forelse($cards as $card)
            @if($card['kind'] === 'order')
                @php
                    $order = $card['model'];

                    $tableLabel = $order->table_number
                        ?? $order->table?->number
                        ?? '-';

                    $tableName = $order->table_name
                        ?? $order->table?->name
                        ?? null;

                    $orderItems = $order->items ?? collect();

                    $typeLabel = $order->order_type ?? 'Dine-In';

                    $isReady = in_array($order->status, ['ready'], true);
                    $isDone = in_array($order->status, ['completed', 'done'], true);
                @endphp

                <article class="operation-card order-card">
                    <div class="operation-card-head">
                        <label class="mini-check">
                            <input type="checkbox">
                            <span></span>
                        </label>

                        <span class="table-chip">
                            T{{ $tableLabel }}
                        </span>

                        <strong class="order-number">
                            #{{ $order->order_number ?? $order->id }}
                        </strong>

                        <span class="time-text">
                            {{ $order->created_at?->diffForHumans() }}
                        </span>
                    </div>

                    <div class="operation-tags">
                        <span class="tag tag-blue">
                            <i class="bi bi-fork-knife"></i>
                            {{ $typeLabel }}
                        </span>

                        <span class="tag {{ $order->status === 'ready' ? 'tag-green' : 'tag-purple' }}">
                            {{ method_exists($order, 'statusLabel') ? $order->statusLabel() : $order->status }}
                        </span>
                    </div>

                    <div class="customer-row">
                        <i class="bi bi-person"></i>
                        <strong>
                            {{ $order->customer_name ?: 'Guest' }}
                        </strong>

                        <span class="order-total">
                            {{ number_format((float) $order->total, 2) }}
                            {{ $order->currency }}
                        </span>
                    </div>

                    @if($tableName)
                        <div class="table-note">
                            {{ $tableName }}
                        </div>
                    @endif

                    <ul class="items-list">
                        @forelse($orderItems->take(4) as $item)
                            <li>
                                <span>
                                    {{ $item->item_name ?? $item->name ?? 'Item' }}
                                </span>

                                <small>
                                    {{ $item->quantity ?? 1 }}
                                </small>
                            </li>
                        @empty
                            <li>
                                <span>لا توجد تفاصيل أصناف</span>
                            </li>
                        @endforelse
                    </ul>

                    <div class="operation-actions">
                        @if(! $isReady && ! $isDone)
                            <form method="POST" action="{{ route('app.restaurant-menu.orders.update-status', [$workspace, $order]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="preparing">

                                <button class="action-btn secondary">
                                    <i class="bi bi-fire"></i>
                                    Preparing
                                </button>
                            </form>

                            <form method="POST" action="{{ route('app.restaurant-menu.orders.update-status', [$workspace, $order]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="ready">

                                <button class="action-btn primary">
                                    <i class="bi bi-check2-circle"></i>
                                    Ready
                                </button>
                            </form>
                        @elseif($isReady)
                            <form method="POST" action="{{ route('app.restaurant-menu.orders.update-status', [$workspace, $order]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">

                                <button class="action-btn primary w-100">
                                    <i class="bi bi-check2-all"></i>
                                    Complete
                                </button>
                            </form>
                        @else
                            <span class="done-label">
                                تم إنهاء الطلب
                            </span>
                        @endif
                    </div>
                </article>
            @else
                @php
                    $serviceRequest = $card['model'];
                    $isWaiter = $serviceRequest->type === 'waiter';
                @endphp

                <article class="operation-card request-card {{ $isWaiter ? 'waiter-request' : 'cash-request' }}">
                    <div class="operation-card-head">
                        <div class="request-icon">
                            <i class="bi {{ $isWaiter ? 'bi-bell' : 'bi-receipt' }}"></i>
                        </div>

                        <strong class="order-number">
                            {{ $serviceRequest->typeLabel() }}
                        </strong>

                        <span class="time-text">
                            {{ $serviceRequest->created_at?->diffForHumans() }}
                        </span>
                    </div>

                    <div class="request-table">
                        <span class="table-chip">
                            T{{ $serviceRequest->table_number ?: $serviceRequest->table?->number ?: '-' }}
                        </span>

                        <div>
                            <strong>
                                {{ $serviceRequest->table_name ?: $serviceRequest->table?->name ?: 'طاولة' }}
                            </strong>

                            <small>
                                {{ $serviceRequest->branch?->name ?: '-' }}
                            </small>
                        </div>
                    </div>

                    <div class="request-message">
                        @if($isWaiter)
                            هذه الطاولة تريد الجرسون.
                        @else
                            هذه الطاولة تريد الحساب / الفاتورة.
                        @endif
                    </div>

                    <div class="operation-actions">
                        @if($serviceRequest->status === 'new')
                            <form method="POST" action="{{ route('app.restaurant-menu.service-requests.update-status', [$workspace, $serviceRequest]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="seen">

                                <button class="action-btn secondary">
                                    تمت المشاهدة
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('app.restaurant-menu.service-requests.update-status', [$workspace, $serviceRequest]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="done">

                            <button class="action-btn primary">
                                تم التنفيذ
                            </button>
                        </form>
                    </div>
                </article>
            @endif
        @empty
            <div class="empty-orders">
                لا توجد طلبات حالية.
            </div>
        @endforelse
    </div>
</div>

<style>
    .orders-master-page {
        --om-red: #c91f26;
        --om-blue: #243f63;
        --om-soft: #f7f7f8;
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
        border: 0;
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

    .mini-check input {
        display: none;
    }

    .mini-check span {
        width: 24px;
        height: 24px;
        border: 2px solid #d8dde6;
        border-radius: 7px;
        display: block;
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

    .customer-row i {
        color: var(--om-muted);
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

    .request-card {
        border-color: transparent;
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
</style>
@endsection