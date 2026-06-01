@extends('app.layouts.app')

@section('title', 'مواعيد العمل')
@section('page_title', 'مواعيد العمل')
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
                    حدد الأيام والساعات المتاحة للحجز أو التشغيل.
                </p>
            </div>

            <a href="{{ route('app.medical.staff.index', $workspace) }}" class="btn btn-light">
                رجوع
            </a>
        </div>

        <form method="POST" action="{{ route('app.medical.staff.working-hours.update', [$workspace, $staff]) }}">
            @csrf
            @method('PUT')

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">تفعيل</th>
                            <th>اليوم</th>
                            <th>الفرع</th>
                            <th>من</th>
                            <th>إلى</th>
                            <th>مدة الموعد</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($days as $dayIndex => $dayLabel)
                            @php
                                $current = optional($workingHours->get($dayIndex))->first();

                                $enabled = old("hours.{$dayIndex}.enabled", $current ? 1 : 0);
                            @endphp

                            <tr>
                                <td>
                                    <input type="hidden" name="hours[{{ $dayIndex }}][enabled]" value="0">

                                    <div class="form-check form-switch">
                                        <input
                                            type="checkbox"
                                            name="hours[{{ $dayIndex }}][enabled]"
                                            value="1"
                                            class="form-check-input"
                                            @checked($enabled)
                                        >
                                    </div>
                                </td>

                                <td>
                                    <strong>{{ $dayLabel }}</strong>
                                </td>

                                <td>
                                    <select name="hours[{{ $dayIndex }}][branch_id]" class="form-select">
                                        <option value="">كل الفروع / حسب العضو</option>

                                        @foreach($branches as $branch)
                                            <option
                                                value="{{ $branch->id }}"
                                                @selected((string) old("hours.{$dayIndex}.branch_id", $current?->branch_id) === (string) $branch->id)
                                            >
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input
                                        type="time"
                                        name="hours[{{ $dayIndex }}][starts_at]"
                                        value="{{ old("hours.{$dayIndex}.starts_at", $current?->starts_at ? \Illuminate\Support\Carbon::parse($current->starts_at)->format('H:i') : '') }}"
                                        class="form-control"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="time"
                                        name="hours[{{ $dayIndex }}][ends_at]"
                                        value="{{ old("hours.{$dayIndex}.ends_at", $current?->ends_at ? \Illuminate\Support\Carbon::parse($current->ends_at)->format('H:i') : '') }}"
                                        class="form-control"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="hours[{{ $dayIndex }}][slot_minutes]"
                                        value="{{ old("hours.{$dayIndex}.slot_minutes", $current?->slot_minutes ?? $staff->default_slot_minutes ?? 30) }}"
                                        class="form-control"
                                        min="5"
                                        max="240"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info rounded-4 mt-3">
                حاليًا كل يوم يدعم فترة واحدة. لاحقًا يمكننا دعم أكثر من فترة في نفس اليوم مثل صباحًا ومساءً.
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('app.medical.staff.index', $workspace) }}" class="btn btn-light">
                    إلغاء
                </a>

                <button class="btn btn-primary">
                    حفظ مواعيد العمل
                </button>
            </div>
        </form>
    </div>
</div>
@endsection