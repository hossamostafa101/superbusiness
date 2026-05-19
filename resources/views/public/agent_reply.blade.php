<!-- resources/views/public/agent_reply.blade.php -->
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>الرد على طلب: {{ $req->request_no }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
  <div class="card">
    <div class="card-body">
      <h5 class="mb-3">الرد على طلب: {{ $req->request_no }} — {{ $req->agent->name }}</h5>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <form method="post" action="{{ route('agent.reply.submit', $req->public_token) }}">
        @csrf
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
              <tr class="text-center">
                <th>مدينة</th><th>فندق</th><th>دخول</th><th>خروج</th><th>ليالي</th>
                <th>نوع</th><th>جمهور</th><th>غرف مطلوبة</th>
                <th>الحالة</th><th>المتاح</th><th>NET/ليلة</th><th>عملة</th><th>ملاحظات</th>
              </tr>
            </thead>
            <tbody>
              @foreach($req->items as $it)
              <tr>
                <td>{{ $it->city ?? optional(optional($it->leg)->hotel)->city }}</td>
                <td>{{ optional($it->hotel)->name ?? optional(optional($it->leg)->hotel)->name }}</td>
                <td>{{ $it->checkin_date }}</td>
                <td>{{ $it->checkout_date }}</td>
                <td>{{ $it->nights }}</td>
                <td>{{ $it->room_type }}</td>
                <td>{{ $it->audience }}</td>
                <td class="text-center">{{ $it->qty_rooms }}</td>

                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $it->id }}">
                <td>
                  <select name="items[{{ $loop->index }}][reply_status]" class="form-select form-select-sm">
                    <option value="available" @selected($it->reply_status==='available')>متاح</option>
                    <option value="partial"   @selected($it->reply_status==='partial')>متاح جزئياً</option>
                    <option value="not_available" @selected($it->reply_status==='not_available')>غير متاح</option>
                  </select>
                </td>
                <td><input type="number" name="items[{{ $loop->index }}][reply_available_rooms]" class="form-control form-control-sm" value="{{ $it->reply_available_rooms }}"></td>
                <td><input type="number" step="0.01" name="items[{{ $loop->index }}][reply_net_rate]" class="form-control form-control-sm" value="{{ $it->reply_net_rate }}"></td>
                <td><input type="text" name="items[{{ $loop->index }}][reply_currency]" class="form-control form-control-sm" value="{{ $it->reply_currency ?? 'SAR' }}"></td>
                <td><input type="text" name="items[{{ $loop->index }}][reply_notes]" class="form-control form-control-sm" value="{{ $it->reply_notes }}"></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-success btn-sm">إرسال الرد</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
