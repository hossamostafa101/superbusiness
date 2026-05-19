@php($seo = $seo ?? ($model->seo ?? null))
@if(session('seo_ok')) <div class="alert alert-success py-2">{{ session('seo_ok') }}</div>@endif

<div class="card mb-3">
  <div class="card-header fw-bold">SEO</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label">عنوان الميتا (≤ 60)</label>
        <input name="seo[meta_title]" class="form-control"
               value="{{ old('seo.meta_title', $seo->meta_title ?? '') }}">
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <small class="text-muted">سيُضاف لاحقًا: {{ config('seo.title_suffix') }}</small>
      </div>

      <div class="col-12">
        <label class="form-label">وصف الميتا (≤ 155)</label>
        <textarea name="seo[meta_description]" rows="2" class="form-control">{{ old('seo.meta_description', $seo->meta_description ?? '') }}</textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">الكلمات المستهدفة</label>
        <input name="seo[focus_keyphrase]" class="form-control"
               value="{{ old('seo.focus_keyphrase', $seo->focus_keyphrase ?? '') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Canonical URL</label>
        <input name="seo[canonical_url]" class="form-control" dir="ltr"
               value="{{ old('seo.canonical_url', $seo->canonical_url ?? '') }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">OG العنوان</label>
        <input name="seo[og_title]" class="form-control" value="{{ old('seo.og_title', $seo->og_title ?? '') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">OG الوصف</label>
        <input name="seo[og_description]" class="form-control" value="{{ old('seo.og_description', $seo->og_description ?? '') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">OG الصورة (رابط)</label>
        <input name="seo[og_image]" class="form-control" dir="ltr" value="{{ old('seo.og_image', $seo->og_image ?? '') }}">
      </div>

      <div class="col-md-6">
        <label class="form-label">Twitter العنوان</label>
        <input name="seo[twitter_title]" class="form-control" value="{{ old('seo.twitter_title', $seo->twitter_title ?? '') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Twitter الوصف</label>
        <input name="seo[twitter_description]" class="form-control" value="{{ old('seo.twitter_description', $seo->twitter_description ?? '') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Twitter الصورة (رابط)</label>
        <input name="seo[twitter_image]" class="form-control" dir="ltr" value="{{ old('seo.twitter_image', $seo->twitter_image ?? '') }}">
      </div>

      <div class="col-md-12">
        <label class="form-label">JSON-LD مخصص (اختياري)</label>
        <textarea name="seo[jsonld]" rows="4" class="form-control" dir="ltr">{{ old('seo.jsonld', $seo->jsonld ?? '') }}</textarea>
      </div>

      <div class="col-12">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="seo[robots_noindex]" value="1"
                 {{ old('seo.robots_noindex', $seo->robots_noindex ?? false) ? 'checked' : '' }}>
          <label class="form-check-label">Noindex</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="seo[robots_nofollow]" value="1"
                 {{ old('seo.robots_nofollow', $seo->robots_nofollow ?? false) ? 'checked' : '' }}>
          <label class="form-check-label">Nofollow</label>
        </div>
      </div>
    </div>
  </div>
</div>
