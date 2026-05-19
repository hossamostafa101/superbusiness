@extends('admin.layout.admin_app')

@section('title', 'إرسال إشعارات FCM')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">إرسال إشعارات Push</h4>

    {{-- رسائل النجاح / الخطأ --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="topic-tab" data-bs-toggle="tab" data-bs-target="#by-topic"
                    type="button" role="tab" aria-controls="by-topic" aria-selected="true">
                إرسال إلى Topic
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="device-tab" data-bs-toggle="tab" data-bs-target="#by-device"
                    type="button" role="tab" aria-controls="by-device" aria-selected="false">
                إرسال إلى جهاز معيّن
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- تبويب: إرسال إلى Topic --}}
        <div class="tab-pane fade show active" id="by-topic" role="tabpanel" aria-labelledby="topic-tab">
            <div class="card mb-4">
                <div class="card-header">
                    إرسال إشعار إلى Topic
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.push_notifications.topic') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Topic</label>
                            <input type="text" name="topic" class="form-control"
                                   placeholder="مثال: koshy أو sonic_driver"
                                   value="{{ old('topic', 'koshy') }}">
                            <div class="form-text">
                                يجب أن يكون نفس الـ topic الذي يشترك عليه التطبيق باستخدام FirebaseMessaging.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">عنوان الإشعار (head)</label>
                            <input type="text" name="head" class="form-control"
                                   value="{{ old('head') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نص الإشعار (desc)</label>
                            <textarea name="desc" class="form-control" rows="3" required>{{ old('desc') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">mtopic (اختياري)</label>
                                <input type="text" name="mtopic" class="form-control"
                                       placeholder="مثال: drivers / vendors"
                                       value="{{ old('mtopic') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">action (اختياري)</label>
                                <input type="text" name="action" class="form-control"
                                       placeholder="مثال: open_current_orders / request_location"
                                       value="{{ old('action', 'open') }}">
                                <div class="form-text">
                                    يُستخدم داخل التطبيق لتحديد نوع الحركة.
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">رابط (url) اختياري</label>
                                <input type="text" name="url" class="form-control"
                                       placeholder="رابط داخلي أو خارجي"
                                       value="{{ old('url') }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> إرسال إلى Topic
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- تبويب: إرسال إلى جهاز معيّن --}}
        <div class="tab-pane fade" id="by-device" role="tabpanel" aria-labelledby="device-tab">
            <div class="card mb-4">
                <div class="card-header">
                    إرسال إشعار إلى جهاز معيّن (FCM Token)
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.push_notifications.device') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">FCM Token للجهاز</label>
                            <textarea name="fcm_token" class="form-control" rows="2" required
                                      placeholder="ضع هنا الـ FCM token الخاص بالجهاز">{{ old('fcm_token') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">عنوان الإشعار (head)</label>
                            <input type="text" name="head" class="form-control"
                                   value="{{ old('head') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نص الإشعار (desc)</label>
                            <textarea name="desc" class="form-control" rows="3" required>{{ old('desc') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">mtopic (اختياري)</label>
                                <input type="text" name="mtopic" class="form-control"
                                       value="{{ old('mtopic') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">action (اختياري)</label>
                                <input type="text" name="action" class="form-control"
                                       placeholder="مثال: open_current_orders / request_location"
                                       value="{{ old('action', 'open') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">رابط (url) اختياري</label>
                                <input type="text" name="url" class="form-control"
                                       value="{{ old('url') }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send-check"></i> إرسال إلى هذا الجهاز
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
