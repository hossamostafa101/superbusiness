{{-- resources/views/restaurant/plans/checkout.blade.php --}}
@extends('restaurant.layouts.app')

@section('title', 'إتمام الاشتراك في الخطة')

@section('content')
<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-bold mb-1">من فضلك راجع الأخطاء التالية:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- العنوان --}}
    <div class="mb-4 text-center">
        <h1 class="h4 mb-2">إتمام الاشتراك في الخطة: {{ $plan->name }}</h1>
        <p class="text-muted mb-0">
            المطعم: <strong>{{ $restaurant->name }}</strong>
        </p>
    </div>

    <div class="row g-4">

        {{-- ملخص الخطة --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3">ملخص الخطة</h2>

                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">اسم الخطة:</span>
                        <span class="fw-semibold">{{ $plan->name }}</span>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">السعر:</span>
                        <span class="fw-semibold">{{ number_format($plan->price, 2) }}</span>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">مدة الاشتراك:</span>
                        <span class="fw-semibold">
                            {{ $plan->duration_days }} يوم
                        </span>
                    </div>

                    <hr class="my-3">

                    <p class="small text-muted mb-0">
                        بعد إرسال طلب الدفع ورفع صورة الإيصال، سيتم مراجعة التحويل من قِبل الإدارة وتفعيل الاشتراك يدويًا في أقرب وقت.
                    </p>
                </div>
            </div>
        </div>

        {{-- طرق الدفع + الفورم --}}
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 mb-3">بيانات التحويل</h2>

                    {{-- عرض أرقام InstaPay وVodafone Cash --}}
                    <div class="row g-3 mb-4">
                        @if(isset($manualMethods['instapay']))
                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="fw-semibold mb-1">
                                        {{ $manualMethods['instapay']['name'] ?? 'InstaPay' }}
                                    </div>
                                    @if(!empty($manualMethods['instapay']['identifier']))
                                        <div class="small mb-1">
                                            <span class="text-muted">الحساب:</span>
                                            <span class="fw-semibold">{{ $manualMethods['instapay']['identifier'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($manualMethods['instapay']['description']))
                                        <p class="small text-muted mb-0">
                                            {{ $manualMethods['instapay']['description'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(isset($manualMethods['vodafone_cash']))
                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="fw-semibold mb-1">
                                        {{ $manualMethods['vodafone_cash']['name'] ?? 'Vodafone Cash' }}
                                    </div>
                                    @if(!empty($manualMethods['vodafone_cash']['number']))
                                        <div class="small mb-1">
                                            <span class="text-muted">الرقم:</span>
                                            <span class="fw-semibold">{{ $manualMethods['vodafone_cash']['number'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($manualMethods['vodafone_cash']['description']))
                                        <p class="small text-muted mb-0">
                                            {{ $manualMethods['vodafone_cash']['description'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(isset($manualMethods['wallet_cash']))
                            <div class="col-12">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="fw-semibold mb-1">
                                        {{ $manualMethods['wallet_cash']['name'] ?? 'محفظة إلكترونية' }}
                                    </div>
                                    @if(!empty($manualMethods['wallet_cash']['number']))
                                        <div class="small mb-1">
                                            <span class="text-muted">الرقم:</span>
                                            <span class="fw-semibold">{{ $manualMethods['wallet_cash']['number'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($manualMethods['wallet_cash']['description']))
                                        <p class="small text-muted mb-0">
                                            {{ $manualMethods['wallet_cash']['description'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- الفورم --}}
                    <form method="POST"
                          action="{{ route('restaurant.plans.manual-payment', $plan) }}"
                          enctype="multipart/form-data">
                        @csrf

                        {{-- طريقة الدفع --}}
                        <div class="mb-3">
                            <label for="method" class="form-label">
                                طريقة الدفع <span class="text-danger">*</span>
                            </label>
                            <select id="method"
                                    name="method"
                                    class="form-select @error('method') is-invalid @enderror"
                                    required>
                                <option value="">-- اختر طريقة الدفع --</option>
                                @foreach($manualMethods as $key => $method)
                                    <option value="{{ $key }}"
                                        {{ old('method') === $key ? 'selected' : '' }}>
                                        {{ $method['name'] ?? $key }}
                                    </option>
                                @endforeach
                            </select>
                            @error('method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
                        </div>

                        {{-- اسم المرسل --}}
                        <div class="mb-3">
                            <label for="sender_name" class="form-label">
                                اسم صاحب التحويل (اختياري)
                            </label>
                            <input type="text"
                                   id="sender_name"
                                   name="sender_name"
                                   class="form-control @error('sender_name') is-invalid @enderror"
                                   value="{{ old('sender_name') }}"
                                   placeholder="الاسم كما يظهر في تطبيق البنك / المحفظة">
                            @error('sender_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- رقم هاتف المرسل --}}
                        <div class="mb-3">
                            <label for="sender_phone" class="form-label">
                                رقم هاتف صاحب التحويل (اختياري)
                            </label>
                            <input type="text"
                                   id="sender_phone"
                                   name="sender_phone"
                                   class="form-control @error('sender_phone') is-invalid @enderror"
                                   value="{{ old('sender_phone') }}"
                                   placeholder="رقم الهاتف المرتبط بالمحفظة أو الحساب">
                            @error('sender_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- صورة الإيصال --}}
                        <div class="mb-3">
                            <label for="receipt_image" class="form-label">
                                صورة إيصال التحويل <span class="text-danger">*</span>
                            </label>
                            <input type="file"
                                   id="receipt_image"
                                   name="receipt_image"
                                   class="form-control @error('receipt_image') is-invalid @enderror"
                                   accept="image/*"
                                   required>
                            @error('receipt_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                من فضلك التقط لقطة شاشة / صورة واضحة توضح بيانات التحويل.
                                (الحد الأقصى 5 ميجابايت)
                            </div>
                        </div>

                        {{-- ملاحظة من المطعم (اختياري) --}}
                        {{-- ⚠ ملاحظة: لو حابب تخزن هذه الملاحظة في قاعدة البيانات،
                             أضف عمود مثلاً customer_note إلى جدول manual_payment_requests
                             وحدث الـ Model + Controller accordingly. --}}
                        <div class="mb-3">
                            <label for="customer_note" class="form-label">
                                ملاحظات إضافية (اختياري)
                            </label>
                            <textarea id="customer_note"
                                      name="customer_note"
                                      class="form-control"
                                      rows="2"
                                      placeholder="أي تفاصيل إضافية تحب توضيحها عن التحويل (رقم عملية، وقت التحويل، ...).">{{ old('customer_note') }}</textarea>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                إرسال طلب الدفع للمراجعة
                            </button>

                            <a href="{{ route('restaurant.plans.index') }}"
                               class="btn btn-outline-secondary">
                                الرجوع لخطط الاشتراك
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
