@extends('app.layouts.app')

@section('title', 'لوحة المطعم')
@section('page_title', 'لوحة المطعم')
@section('page_description', 'ملخص التشغيل والطلبات ونقطة البيع.')

@section('content')
<style>
    .rm-grid {
        display: grid;
        gap: 16px;
    }

    .rm-stats {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .rm-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 18px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
    }

    .rm-stat {
        min-height: 132px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .rm-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        background: #f1f5f9;
        color: #0f172a;
        font-size: 20px;
    }

    .rm-stat-value {
        font-size: 28px;
        font-weight: 950;
        line-height: 1;
    }

    .rm-stat-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    .rm-actions {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .rm-action {
        min-height: 86px;
        border-radius: 20px;
        padding: 14px;
        background: #0f172a;
        color: #fff;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: .15s ease;
    }

    .rm-action:hover {
        color: #fff;
        transform: translateY(-2px);
    }

    .rm-action i {
        font-size: 22px;
    }

    .rm-action span {
        font-weight: 900;
    }

    .rm-row {
        display: grid;
        grid-template-columns: 1.4fr .9fr;
        gap: 16px;
    }

    .rm-order {
        border: 1px solid #eef2f7;
        border-radius: 16px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .rm-order:last-child {
        margin-bottom: 0;
    }

    .rm-badge {
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 900;
    }

    .rm-status-new { background: #dbeafe; color: #1d4ed8; }
    .rm-status-accepted { background: #dcfce7; color: #15803d; }
    .rm-status-preparing { background: #ffedd5; color: #c2410c; }
    .rm-status-ready { background: #fef9c3; color: #a16207; }
    .rm-status-completed { background: #e5e7eb; color: #374151; }
    .rm-status-cancelled { background: #fee2e2; color: #b91c1c; }

    @media (max-width: 1200px) {
        .rm-stats,
        .rm-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rm-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .rm-stats,
        .rm-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="rm-grid">
    <div class="rm-actions">
        @if(\Illuminate\Support\Facades\Route::has('app.restaurant-menu.pos.index'))
            <a href="{{ route('app.restaurant-menu.pos.index', $workspace) }}" class="rm-action">
                <i class="bi bi-calculator"></i>
                <span>فتح POS</span>
            </a>
        @endif

        <a href="{{ route('app.restaurant-menu.orders.index', $workspace) }}" class="rm-action">
            <i class="bi bi-receipt"></i>
            <span>إدارة الطلبات</span>
        </a>

        <a href="{{ route('app.restaurant-menu.items.index', $workspace) }}" class="rm-action">
            <i class="bi bi-cup-hot"></i>
            <span>إضافة صنف</span>
        </a>

        @if($defaultBranch)
            <a href="{{ route('public.restaurant-menu.branch', [$workspace, $defaultBranch]) }}" target="_blank" class="rm-action">
                <i class="bi bi-box-arrow-up-left"></i>
                <span>فتح المنيو</span>
            </a>
        @else
            <a href="{{ route('app.restaurant-menu.branches.index', $workspace) }}" class="rm-action">
                <i class="bi bi-shop"></i>
                <span>إضافة فرع</span>
            </a>
        @endif
    </div>

    <div class="rm-grid rm-stats">
        <div class="rm-card rm-stat">
            <div class="d-flex justify-content-between align-items-start">
                <div class="rm-stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>

                @if(!is_null($salesChangePercent))
                    <span class="badge {{ $salesChangePercent >= 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ $salesChangePercent >= 0 ? '+' : '' }}{{ $salesChangePercent }}%
                    </span>
                @endif
            </div>

            <div>
                <div class="rm-stat-value">
                    {{ number_format((float) $todaySalesTotal, 2) }}
                </div>
                <div class="rm-stat-label">مبيعات اليوم</div>
            </div>
        </div>

        <div class="rm-card rm-stat">
            <div class="rm-stat-icon">
                <i class="bi bi-bag-check"></i>
            </div>

            <div>
                <div class="rm-stat-value">{{ $todayOrdersCount }}</div>
                <div class="rm-stat-label">طلبات اليوم</div>
            </div>
        </div>

        <div class="rm-card rm-stat">
            <div class="rm-stat-icon">
                <i class="bi bi-lightning-charge"></i>
            </div>

            <div>
                <div class="rm-stat-value">{{ $activeOrdersCount }}</div>
                <div class="rm-stat-label">طلبات نشطة</div>
            </div>
        </div>

        <div class="rm-card rm-stat">
            <div class="rm-stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>

            <div>
                <div class="rm-stat-value">{{ $openShiftsCount }}</div>
                <div class="rm-stat-label">شيفتات مفتوحة</div>
            </div>
        </div>
    </div>

    <div class="rm-grid rm-stats">
        <a href="{{ route('app.restaurant-menu.orders.index', [$workspace, 'status' => 'new']) }}" class="rm-card text-decoration-none text-dark">
            <div class="d-flex justify-content-between">
                <strong>طلبات جديدة</strong>
                <span class="rm-badge rm-status-new">{{ $newOrdersCount }}</span>
            </div>
        </a>

        <a href="{{ route('app.restaurant-menu.orders.index', [$workspace, 'status' => 'accepted']) }}" class="rm-card text-decoration-none text-dark">
            <div class="d-flex justify-content-between">
                <strong>مقبولة</strong>
                <span class="rm-badge rm-status-accepted">{{ $acceptedOrdersCount }}</span>
            </div>
        </a>

        <a href="{{ route('app.restaurant-menu.orders.index', [$workspace, 'status' => 'preparing']) }}" class="rm-card text-decoration-none text-dark">
            <div class="d-flex justify-content-between">
                <strong>قيد التحضير</strong>
                <span class="rm-badge rm-status-preparing">{{ $preparingOrdersCount }}</span>
            </div>
        </a>

        <a href="{{ route('app.restaurant-menu.orders.index', [$workspace, 'status' => 'ready']) }}" class="rm-card text-decoration-none text-dark">
            <div class="d-flex justify-content-between">
                <strong>جاهزة</strong>
                <span class="rm-badge rm-status-ready">{{ $readyOrdersCount }}</span>
            </div>
        </a>
    </div>

    @if($openInvoiceEnabled)
        <div class="rm-grid rm-stats">
            <div class="rm-card">
                <div class="d-flex justify-content-between">
                    <strong>جلسات مفتوحة</strong>
                    <span class="badge bg-primary">{{ $openInvoicesCount }}</span>
                </div>
            </div>

            <div class="rm-card">
                <div class="d-flex justify-content-between">
                    <strong>تنتهي قريبًا</strong>
                    <span class="badge bg-warning text-dark">{{ $endingSoonInvoicesCount }}</span>
                </div>
            </div>

            <div class="rm-card">
                <div class="d-flex justify-content-between">
                    <strong>منتهية</strong>
                    <span class="badge bg-danger">{{ $expiredOpenInvoicesCount }}</span>
                </div>
            </div>

            <div class="rm-card">
                <div class="d-flex justify-content-between">
                    <strong>طلبات خدمة</strong>
                    <span class="badge bg-dark">{{ $pendingServiceRequestsCount }}</span>
                </div>
            </div>
        </div>
    @else
        <div class="rm-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="h6 fw-bold mb-1">نظام جلسات الطاولات غير مفعل</h2>
                    <p class="text-muted mb-0">
                        الداشبورد يعمل حاليًا بنظام الطلبات العادية. يمكن تفعيل جلسات الطاولات من الإعدادات عند الحاجة.
                    </p>
                </div>

                @if(\Illuminate\Support\Facades\Route::has('app.restaurant-menu.settings.index'))
                    <a href="{{ route('app.restaurant-menu.settings.index', [$workspace, 'tab' => 'pos']) }}" class="btn btn-outline-dark">
                        الإعدادات
                    </a>
                @endif
            </div>
        </div>
    @endif

    <div class="rm-row">
        <div class="rm-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">آخر الطلبات</h2>

                <a href="{{ route('app.restaurant-menu.orders.index', $workspace) }}" class="btn btn-sm btn-outline-dark">
                    عرض الكل
                </a>
            </div>

            @forelse($latestOrders as $order)
                @php
                    $statusClass = 'rm-status-' . $order->status;
                @endphp

                <div class="rm-order">
                    <div class="d-flex justify-content-between gap-2">
                        <div>
                            <strong>#{{ $order->order_number }}</strong>

                            <div class="small text-muted">
                                {{ $order->branch?->name ?? '—' }}
                                ·
                                {{ $order->created_at?->format('H:i') }}
                                ·
                                {{ $order->items_count }} صنف
                            </div>
                        </div>

                        <span class="rm-badge {{ $statusClass }}">
                            {{ $order->status }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <strong>
                            {{ number_format((float) $order->total, 2) }}
                            {{ $order->currency }}
                        </strong>

                        <a href="{{ route('app.restaurant-menu.orders.show', [$workspace, $order]) }}" class="btn btn-sm btn-light">
                            التفاصيل
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    لا توجد طلبات بعد.
                </div>
            @endforelse
        </div>

        <div class="rm-grid">
            <div class="rm-card">
                <h2 class="h5 fw-bold mb-3">ملخص المنيو</h2>

                <div class="d-flex justify-content-between mb-2">
                    <span>الفروع</span>
                    <strong>{{ $branchesCount }}</strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>الأصناف</span>
                    <strong>{{ $itemsCount }}</strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>الأصناف المتاحة</span>
                    <strong>{{ $availableItemsCount }}</strong>
                </div>
            </div>

            <div class="rm-card">
                <h2 class="h5 fw-bold mb-3">تشغيل اليوم</h2>

                <div class="d-flex justify-content-between mb-2">
                    <span>طلبات مكتملة</span>
                    <strong>{{ $completedTodayOrdersCount }}</strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>طلبات ملغاة</span>
                    <strong>{{ $cancelledTodayOrdersCount }}</strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>طلبات خدمة</span>
                    <strong>{{ $pendingServiceRequestsCount }}</strong>
                </div>
            </div>

            @if($latestOpenShifts->count())
                <div class="rm-card">
                    <h2 class="h5 fw-bold mb-3">الشيفتات المفتوحة</h2>

                    @foreach($latestOpenShifts as $shift)
                        <div class="border rounded-4 p-2 mb-2">
                            <strong>#{{ $shift->id }}</strong>

                            <div class="small text-muted">
                                {{ $shift->branch?->name ?? '—' }}
                                @if($shift->register)
                                    · {{ $shift->register->name }}
                                @endif
                                @if($shift->staff)
                                    · {{ $shift->staff->name }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection