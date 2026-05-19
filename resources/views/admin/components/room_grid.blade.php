@props([
  'pairs' => collect(), // مصفوفة/مجموعة من الأزواج
  'title' => null,
])

<div class="room-grid-wrapper">
  @if($title)
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="m-0">{{ $title }}</h6>
      <div class="legend d-flex gap-2 small">
        <span class="legend-item"><span class="legend-dot male"></span> رجال</span>
        <span class="legend-item"><span class="legend-dot female"></span> سيدات</span>
        <span class="legend-item"><span class="legend-dot family"></span> عائلي</span>
        <span class="legend-item"><span class="legend-dot empty"></span> فارغ</span>
        <span class="legend-item"><span class="legend-dot occupied"></span> محجوز</span>
      </div>
    </div>
  @endif

  <div class="grid">
    @forelse($pairs as $p)
      @php
        $cap   = (int)($p->capacity_beds ?? 0);
        $used  = (int)($p->beds_used ?? 0);
        $lock  = $p->gender_lock ?? 'none'; // men / women / family / none
        $open  = (int)($p->is_open ?? 0) === 1;
        $type  = $p->room_type ?? 'room';
        $cells = max($cap, 1);
      @endphp

      <div class="gcard">
        <div class="gcard-hd">
          <div class="name">غرفة #{{ $p->id }} — {{ strtoupper($type) }}</div>
          <div class="meta">
            سعة: {{ $cap }} — مستخدم: {{ $used }}
            @if($open)
              <span class="badge text-bg-success">Open</span>
            @else
              <span class="badge text-bg-secondary">Closed</span>
            @endif
          </div>
        </div>
        <div class="gcard-bd">
          @for($i=1; $i <= $cells; $i++)
            @php
              $occupied = $i <= $used;
              // لون الخلية حسب سياسة النوع + الحالة
              $genderClass = $lock === 'men' ? 'male' : ($lock === 'women' ? 'female' : ($lock === 'family' ? 'family' : 'none'));
              $stateClass  = $occupied ? 'occupied' : 'empty';
            @endphp
            <div class="gcell {{ $genderClass }} {{ $stateClass }}" title="سرير {{ $i }}">
              <span class="idx">{{ $i }}</span>
            </div>
          @endfor
        </div>
      </div>
    @empty
      <div class="text-muted small py-2">لا توجد أزواج غرف متاحة.</div>
    @endforelse
  </div>
</div>
