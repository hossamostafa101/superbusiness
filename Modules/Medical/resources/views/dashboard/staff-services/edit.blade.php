@extends('app.layouts.app')

@section('title', 'خدمات عضو الفريق')
@section('page_title', 'خدمات عضو الفريق')
@section('page_description', $staff->name)

@section('content')
<div class="card content-card">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 fw-bold mb-1">
                    {{ $staff->name }}
                </h2>

                <p class="text-muted mb-0">
                    اختر الخدمات التي يقدمها هذا العضو مع إمكانية تحديد سعر أو مدة مختلفة.
                </p>
            </div>

            <a href="{{ route('app.medical.staff.index', $workspace) }}" class="btn btn-light">
                رجوع
            </a>
        </div>

        <form method="POST" action="{{ route('app.medical.staff.services.update', [$workspace, $staff]) }}">
            @csrf
            @method('PUT')

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">تفعيل</th>
                            <th>الخدمة</th>
                            <th>القسم</th>
                            <th>السعر الأساسي</th>
                            <th>سعر خاص</th>
                            <th>المدة الأساسية</th>
                            <th>مدة خاصة</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($services as $service)
                            @php
                                $assigned = $assignedServices->get($service->id);
                                $enabled = old("services.{$service->id}.enabled", $assigned ? 1 : 0);
                            @endphp

                            <tr>
                                <td>
                                    <input type="hidden" name="services[{{ $service->id }}][enabled]" value="0">

                                    <div class="form-check form-switch">
                                        <input
                                            type="checkbox"
                                            name="services[{{ $service->id }}][enabled]"
                                            value="1"
                                            class="form-check-input"
                                            @checked($enabled)
                                        >
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-bold">
                                        {{ $service->name }}
                                    </div>

                                    <div class="small text-muted">
                                        {{ $service->typeLabel() }}
                                    </div>
                                </td>

                                <td>
                                    {{ $service->department?->name ?: '-' }}
                                </td>

                                <td>
                                    @if($service->price !== null)
                                        {{ number_format((float) $service->price, 2) }}
                                        {{ $service->currency }}
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="services[{{ $service->id }}][price_override]"
                                        value="{{ old("services.{$service->id}.price_override", $assigned?->price_override) }}"
                                        class="form-control"
                                        placeholder="اختياري"
                                    >
                                </td>

                                <td>
                                    @if($service->duration_minutes)
                                        {{ $service->duration_minutes }} دقيقة
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        min="1"
                                        max="1440"
                                        name="services[{{ $service->id }}][duration_override]"
                                        value="{{ old("services.{$service->id}.duration_override", $assigned?->duration_override) }}"
                                        class="form-control"
                                        placeholder="اختياري"
                                    >
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    لا توجد خدمات نشطة بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.staff.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ الخدمات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection