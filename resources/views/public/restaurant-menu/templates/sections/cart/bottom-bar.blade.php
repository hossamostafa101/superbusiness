{{-- resources/views/public/restaurant-menu/templates/sections/cart/bottom-bar.blade.php --}}
<div class="cart-float cart-bottom-bar" id="cartFloat" style="display:none;">
    <button type="button" class="cart-button cart-button-bottom-bar" data-bs-toggle="modal" data-bs-target="#cartModal">
        <span class="cart-bottom-icon">
            <i class="bi bi-bag"></i>
        </span>

        <span class="flex-grow-1 text-start">
            مراجعة الطلب
            <small class="d-block opacity-75">اضغط لإرسال الطلب</small>
        </span>

        <strong id="cartFloatTotal">0.00</strong>
    </button>
</div>