<!-- resources/views/mail/availability_request.blade.php -->
@php($agent = $req->agent)
<p>السادة/ {{ $agent->name }}،</p>
<p>نرجو إفادتنا بتوافر الغرف للبنود التالية:</p>

<table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse;">
  <thead>
    <tr>
      <th>المدينة</th><th>الفندق</th><th>دخول</th><th>خروج</th><th>ليالي</th><th>نوع الغرفة</th><th>الجمهور</th><th>عدد الغرف</th>
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
      <td>{{ $it->qty_rooms }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<p>يرجى الرد عبر الرابط التالي (آمن وموقع):<br>
<a href="{{ $signedUrl }}">{{ $signedUrl }}</a></p>

@if($req->due_at)
<p>آخر موعد للرد: {{ \Carbon\Carbon::parse($req->due_at)->format('Y-m-d H:i') }}</p>
@endif

<p>مع الشكر،</p>
