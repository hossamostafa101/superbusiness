{{-- resources/views/app/restaurant-menu/orders/partials/whatsapp-actions.blade.php --}}
@php
    $receivedUrl = $order->whatsappUrl('received');
    $acceptedUrl = $order->whatsappUrl('accepted');
    $readyUrl = $order->whatsappUrl('ready');
    $completedUrl = $order->whatsappUrl('completed');
    $cancelledUrl = $order->whatsappUrl('cancelled');
@endphp

@if($receivedUrl)
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-whatsapp"></i>
            واتساب
        </button>

        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ $receivedUrl }}" target="_blank">
                    تأكيد الاستلام
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="{{ $acceptedUrl }}" target="_blank">
                    تم قبول الطلب
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="{{ $readyUrl }}" target="_blank">
                    الطلب جاهز
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="{{ $completedUrl }}" target="_blank">
                    تم إكمال الطلب
                </a>
            </li>

            <li>
                <a class="dropdown-item text-danger" href="{{ $cancelledUrl }}" target="_blank">
                    إبلاغ بالإلغاء
                </a>
            </li>
        </ul>
    </div>
@else
    <button class="btn btn-sm btn-outline-secondary" disabled>
        لا يوجد هاتف
    </button>
@endif