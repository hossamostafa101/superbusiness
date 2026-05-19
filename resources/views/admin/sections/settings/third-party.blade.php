@extends('admin.layout.admin_app')

@section('title','إعدادات الطرف الثالث')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title mb-3">إعدادات الطرف الثالث</h5>

    @if(session('ok')) <div class="alert alert-success small">{{ session('ok') }}</div> @endif
    @if ($errors->any())
      <div class="alert alert-danger small"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="post" action="{{ route('admin.settings.thirdparty.update') }}" class="vstack gap-4">
      @csrf

      {{-- Google AdSense --}}
      <div class="border rounded p-3">
        <h6 class="mb-3"><i class="bi bi-google me-1 text-danger"></i> Google AdSense</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Client (ca-pub-XXXX)</label>
            <input name="adsense[client]" class="form-control" value="{{ old('adsense.client', $adsense['client']) }}" placeholder="ca-pub-1234567890">
          </div>
          <div class="col-md-6">
            <label class="form-label">Home Top Slot (728×90)</label>
            <input name="adsense[slots][home_top]" class="form-control" value="{{ old('adsense.slots.home_top', $adsense['slots']['home_top'] ?? null) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Sidebar Slot (300×250 أو 300×600)</label>
            <input name="adsense[slots][sidebar]" class="form-control" value="{{ old('adsense.slots.sidebar', $adsense['slots']['sidebar'] ?? null) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">In-Feed Slot</label>
            <input name="adsense[slots][in_feed]" class="form-control" value="{{ old('adsense.slots.in_feed', $adsense['slots']['in_feed'] ?? null) }}">
          </div>
        </div>
      </div>

      {{-- Amazon Affiliate --}}
      <div class="border rounded p-3">
        <h6 class="mb-3"><i class="bi bi-amazon me-1 text-warning"></i> Amazon Affiliate</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Tracking ID (Associate Tag)</label>
            <input name="amazon[tag]" class="form-control" value="{{ old('amazon.tag', $amazon['tag']) }}" placeholder="yourtag-20">
          </div>
          <div class="col-md-6">
            <label class="form-label">Country</label>
            <select name="amazon[country]" class="form-select">
              @foreach(['us','uk','de','fr','it','es','ca','jp','ae','in','br','mx','tr','au','nl','sg','se','pl'] as $c)
                <option value="{{ $c }}" @selected(old('amazon.country', $amazon['country'])==$c)>{{ strtoupper($c) }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <hr>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" id="paapiEnabled" name="amazon[paapi][enabled]" value="1"
                 {{ old('amazon.paapi.enabled', data_get($amazon,'paapi.enabled')) ? 'checked' : '' }}>
          <label class="form-check-label" for="paapiEnabled">تفعيل Amazon PA-API (اختياري)</label>
        </div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Access Key</label>
            <input name="amazon[paapi][access_key]" class="form-control" value="{{ old('amazon.paapi.access_key', data_get($amazon,'paapi.access_key')) }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Secret Key</label>
            <input name="amazon[paapi][secret_key]" class="form-control" value="{{ old('amazon.paapi.secret_key', data_get($amazon,'paapi.secret_key')) }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Associate Tag</label>
            <input name="amazon[paapi][associate_tag]" class="form-control" value="{{ old('amazon.paapi.associate_tag', data_get($amazon,'paapi.associate_tag')) }}">
          </div>
        </div>
        <div class="form-text">لو PA-API غير مفعلة سنستخدم روابط أفيلييت مباشرة بإضافة ?tag=.</div>
      </div>

      <div>
        <button class="btn btn-info text-white"><i class="bi bi-check2"></i> حفظ الإعدادات</button>
      </div>
    </form>
  </div>
</div>
@endsection
