{{-- resources/views/app/business-appointments/partials/whatsapp-actions.blade.php --}}
@php
    $confirmUrl = $appointment->whatsappUrl('confirm');
    $reminderUrl = $appointment->whatsappUrl('reminder');
    $cancelUrl = $appointment->whatsappUrl('cancel');
@endphp

@if($confirmUrl || $reminderUrl || $cancelUrl)
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-whatsapp"></i>
            واتساب
        </button>

        <ul class="dropdown-menu">
            @if($confirmUrl)
                <li>
                    <a class="dropdown-item" href="{{ $confirmUrl }}" target="_blank">
                        تأكيد الموعد
                    </a>
                </li>
            @endif

            @if($reminderUrl)
                <li>
                    <a class="dropdown-item" href="{{ $reminderUrl }}" target="_blank">
                        تذكير بالموعد
                    </a>
                </li>
            @endif

            @if($cancelUrl)
                <li>
                    <a class="dropdown-item text-danger" href="{{ $cancelUrl }}" target="_blank">
                        إبلاغ بالإلغاء
                    </a>
                </li>
            @endif
        </ul>
    </div>
@else
    <button class="btn btn-sm btn-outline-secondary" disabled>
        لا يوجد هاتف
    </button>
@endif