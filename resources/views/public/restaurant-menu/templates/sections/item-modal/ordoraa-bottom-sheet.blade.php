{{-- resources/views/public/restaurant-menu/templates/sections/item-modal/ordoraa-bottom-sheet.blade.php --}}
<div class="modal fade od-item-sheet" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable od-sheet-dialog">
        <div class="modal-content od-sheet-content">
            <div class="od-sheet-handle"></div>

            <div id="modalImageWrap"></div>

            <div class="modal-body od-sheet-body">
                <div class="d-flex justify-content-between gap-3 align-items-start mb-2">
                    <div>
                        <h3 class="od-modal-title" id="modalTitle"></h3>
                        <p class="od-modal-desc" id="modalDescription"></p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="d-flex gap-2 flex-wrap small text-muted mb-3" id="modalMeta"></div>

                <div class="od-modal-price-card mb-3">
                    <div>
                        <div class="small text-muted">السعر</div>
                        <div class="od-modal-price" id="modalPrice"></div>
                        <div class="old-price" id="modalOldPrice"></div>
                    </div>
                </div>

                <div id="modalVariants"></div>
                <div id="modalOptionGroups"></div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">الكمية</label>

                    <div class="od-qty-control">
                        <button type="button" id="qtyMinus">-</button>

                        <input
                            type="number"
                            id="modalQty"
                            value="1"
                            min="1"
                            max="100"
                        >

                        <button type="button" id="qtyPlus">+</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">ملاحظات</label>

                    <textarea
                        id="modalItemNotes"
                        class="form-control od-notes-input"
                        rows="2"
                        placeholder="مثال: بدون بصل، زيادة صوص"
                    ></textarea>
                </div>

                <button type="button" class="od-add-to-cart-btn" id="addToCartBtn">
                    إضافة إلى الطلب
                </button>
            </div>
        </div>
    </div>
</div>