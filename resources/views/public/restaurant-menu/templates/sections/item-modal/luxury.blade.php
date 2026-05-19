{{-- resources/views/public/restaurant-menu/templates/sections/item-modal/luxury.blade.php --}}
<div class="modal fade item-luxury-modal" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content luxury-modal-content">
            <div id="modalImageWrap"></div>

            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="luxury-label text-dark mb-2">Menu Item</div>
                    <h3 class="h4 fw-bold mb-1" id="modalTitle"></h3>
                    <p class="text-muted small mb-0" id="modalDescription"></p>
                </div>

                <button type="button" class="btn-close luxury-close" data-bs-dismiss="modal"></button>

                <div class="d-flex justify-content-center gap-2 flex-wrap small text-muted mb-3" id="modalMeta"></div>

                <div class="luxury-price-card mb-3">
                    <div class="small text-muted">السعر</div>
                    <div class="h5 fw-bold mb-0" id="modalPrice"></div>
                    <div class="old-price" id="modalOldPrice"></div>
                </div>

                <div id="modalVariants"></div>
                <div id="modalOptionGroups"></div>

                <div class="mb-3">
                    <label class="form-label">الكمية</label>
                    <div class="d-flex gap-2 align-items-center justify-content-center">
                        <button type="button" class="btn btn-light border" id="qtyMinus">-</button>
                        <input type="number" id="modalQty" value="1" min="1" max="100" class="form-control text-center" style="max-width: 90px;">
                        <button type="button" class="btn btn-light border" id="qtyPlus">+</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">ملاحظات على الصنف</label>
                    <textarea id="modalItemNotes" class="form-control" rows="2" placeholder="مثال: بدون بصل، زيادة صوص"></textarea>
                </div>

                <button type="button" class="btn btn-main w-100 mt-2" id="addToCartBtn">
                    إضافة إلى الطلب
                </button>
            </div>
        </div>
    </div>
</div>