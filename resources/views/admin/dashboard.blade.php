{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layout.admin_app') {{-- غيّرها حسب اسم الـ layout عندك --}}

@section('title', 'لوحة تحكم السوبر أدمن')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== الصف الأول: الكروت الإجمالية ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fw-bold text-muted mb-1">المطاعم</div>
                    <div class="h4 mb-0">{{ number_format($totals['restaurants'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fw-bold text-muted mb-1">الفروع</div>
                    <div class="h4 mb-0">{{ number_format($totals['branches'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fw-bold text-muted mb-1">الأصناف</div>
                    <div class="h4 mb-0">{{ number_format($totals['items'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fw-bold text-muted mb-1">الطلبات</div>
                    <div class="h4 mb-0">{{ number_format($totals['orders'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fw-bold text-muted mb-1">المستخدمون</div>
                    <div class="h4 mb-0">{{ number_format($totals['users'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="fw-bold text-muted mb-1">الاشتراكات</div>
                    <div class="h4 mb-0">{{ number_format($totals['subscriptions'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== الصف الثاني: مخطط زمني + حالات الطلبات ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">الطلبات خلال آخر 30 يومًا</span>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">توزيع حالات الطلبات</span>
                </div>
                <div class="card-body">
                    @if (!empty($orderStatusCounts))
                        <canvas id="statusChart" height="180"></canvas>
                    @else
                        <div class="text-muted text-center py-4">
                            لا توجد بيانات طلبات حاليًا.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== الصف الثالث: آخر الطلبات + أفضل المطاعم ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">آخر الطلبات</span>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="latestOrdersTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab"
                                    data-bs-target="#pendingTab" type="button" role="tab">
                                قيد الانتظار
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inprogress-tab" data-bs-toggle="tab"
                                    data-bs-target="#inprogressTab" type="button" role="tab">
                                جارية
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="completed-tab" data-bs-toggle="tab"
                                    data-bs-target="#completedTab" type="button" role="tab">
                                مكتملة
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Pending --}}
                        <div class="tab-pane fade show active" id="pendingTab" role="tabpanel">
                            @include('admin.partials.latest-orders-table', [
                                'orders' => $latest['pending'] ?? collect(),
                            ])
                        </div>

                        {{-- In Progress --}}
                        <div class="tab-pane fade" id="inprogressTab" role="tabpanel">
                            @include('admin.partials.latest-orders-table', [
                                'orders' => $latest['inprogress'] ?? collect(),
                            ])
                        </div>

                        {{-- Completed --}}
                        <div class="tab-pane fade" id="completedTab" role="tabpanel">
                            @include('admin.partials.latest-orders-table', [
                                'orders' => $latest['completed'] ?? collect(),
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Restaurants --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">أفضل المطاعم (آخر 30 يومًا)</span>
                </div>
                <div class="card-body p-0">
                    @if(isset($topRestaurants) && $topRestaurants->count())
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>المطعم</th>
                                    <th>عدد الطلبات</th>
                                    <th>إجمالي المبيعات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topRestaurants as $idx => $r)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $r->name }}</td>
                                        <td>{{ $r->orders_count }}</td>
                                        <td>{{ number_format($r->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted text-center py-4">
                            لا توجد بيانات كافية لعرض أفضل المطاعم.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    {{-- لو ما عندكش bootstrap/js هنا ضيفه في الـ layout الرئيسي --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ===== بيانات المخطط الزمني =====
        const ordersChartLabels  = @json($labels ?? []);
        const ordersCreatedData  = @json($series['created'] ?? []);
        const ordersCompletedData = @json($series['completed'] ?? []);

        const ordersCtx = document.getElementById('ordersChart');
        if (ordersCtx && ordersChartLabels.length) {
            new Chart(ordersCtx, {
                type: 'line',
                data: {
                    labels: ordersChartLabels,
                    datasets: [
                        {
                            label: 'طلبات جديدة',
                            data: ordersCreatedData,
                            tension: 0.3,
                            borderWidth: 2,
                            fill: false,
                        },
                        {
                            label: 'طلبات مكتملة',
                            data: ordersCompletedData,
                            tension: 0.3,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // ===== بيانات مخطط حالات الطلبات =====
        const statusCounts = @json($orderStatusCounts ?? []);
        const statusLabels = Object.keys(statusCounts);
        const statusData   = Object.values(statusCounts);

        const statusCtx = document.getElementById('statusChart');
        if (statusCtx && statusLabels.length) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    </script>
@endpush
