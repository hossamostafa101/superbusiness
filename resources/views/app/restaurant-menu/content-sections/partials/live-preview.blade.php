<div class="card content-card sticky-top" style="top: 90px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold mb-1">معاينة مباشرة</h6>
                <div class="small text-muted">
                    تتحدث عند تغيير بيانات القسم.
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshContentSectionPreviewBtn">
                تحديث
            </button>
        </div>

        <div class="content-section-preview-frame-wrap">
            <div class="content-section-preview-loading" id="contentSectionPreviewLoading">
                جاري تحديث المعاينة...
            </div>

            <iframe
                id="contentSectionPreviewFrame"
                src="{{ $previewUrl }}?content_section_preview=1"
                class="content-section-preview-frame"
                loading="lazy"
            ></iframe>
        </div>

        <div class="d-grid mt-3">
            <a href="{{ $previewUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                فتح المنيو
            </a>
        </div>
    </div>
</div>

<style>
    .content-section-preview-frame-wrap {
        position: relative;
        width: 100%;
        height: 680px;
        border: 10px solid #111827;
        border-radius: 34px;
        overflow: hidden;
        background: #f3f4f6;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .18);
    }

    .content-section-preview-frame {
        width: 100%;
        height: 100%;
        border: 0;
        background: #fff;
    }

    .content-section-preview-loading {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,.72);
        display: none;
        place-items: center;
        z-index: 2;
        font-size: 13px;
        font-weight: 700;
        color: #111827;
    }

    @media (max-width: 991px) {
        .content-section-preview-frame-wrap {
            height: 620px;
        }
    }
</style>

@push('scripts')
<script>
    (function () {
        const previewBaseUrl = @json($previewUrl);
        const previewFrame = document.getElementById('contentSectionPreviewFrame');
        const previewLoading = document.getElementById('contentSectionPreviewLoading');
        const refreshBtn = document.getElementById('refreshContentSectionPreviewBtn');

        let timer = null;

        function fieldValue(name) {
            const field = document.querySelector(`[name="${name}"]`);

            if (!field) {
                return '';
            }

            return field.value || '';
        }

        function checkedItems() {
            return Array.from(document.querySelectorAll('[name="item_ids[]"]:checked'))
                .map(function (input) {
                    return input.value;
                });
        }

        function buildPreviewUrl() {
            const params = new URLSearchParams();

            params.set('content_section_preview', '1');
            params.set('preview_section_type', fieldValue('type'));
            params.set('preview_branch_id', fieldValue('branch_id'));
            params.set('preview_title', fieldValue('title'));
            params.set('preview_subtitle', fieldValue('subtitle'));

            params.set('preview_background_type', fieldValue('background_type'));
            params.set('preview_background_color', fieldValue('background_color'));
            params.set('preview_background_gradient_from', fieldValue('background_gradient_from'));
            params.set('preview_background_gradient_to', fieldValue('background_gradient_to'));
            params.set('preview_text_color', fieldValue('text_color'));
            params.set('preview_button_color', fieldValue('button_color'));

            checkedItems().forEach(function (itemId) {
                params.append('preview_item_ids[]', itemId);
            });

            params.set('_t', Date.now().toString());

            return previewBaseUrl + '?' + params.toString();
        }

        function updatePreview() {
            if (!previewFrame) {
                return;
            }

            if (previewLoading) {
                previewLoading.style.display = 'grid';
            }

            previewFrame.src = buildPreviewUrl();
        }

        function scheduleUpdate() {
            clearTimeout(timer);

            timer = setTimeout(function () {
                updatePreview();
            }, 450);
        }

        const fields = document.querySelectorAll(`
            [name="type"],
            [name="branch_id"],
            [name="title"],
            [name="subtitle"],
            [name="background_type"],
            [name="background_color"],
            [name="background_gradient_from"],
            [name="background_gradient_to"],
            [name="text_color"],
            [name="button_color"],
            [name="item_ids[]"]
        `);

        fields.forEach(function (field) {
            field.addEventListener('change', scheduleUpdate);
            field.addEventListener('input', scheduleUpdate);
        });

        previewFrame?.addEventListener('load', function () {
            if (previewLoading) {
                previewLoading.style.display = 'none';
            }
        });

        refreshBtn?.addEventListener('click', updatePreview);

        setTimeout(updatePreview, 300);
    })();
</script>
@endpush