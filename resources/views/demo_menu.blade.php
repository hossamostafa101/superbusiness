@extends('layouts.app')

@section('title', $restaurant['name'].' - Menu')

@section('content')
  {{-- Header --}}
  <div class="glass rounded-4 p-3 mb-3">
    <div class="d-flex align-items-start gap-3">
      <div class="rounded-4 d-flex align-items-center justify-content-center"
           style="width:56px;height:56px;background:linear-gradient(135deg, {{$restaurant['cover_color']}}, rgba(34,197,94,.7)); color:#06111b; font-weight:900;">
        {{ mb_substr($restaurant['name'],0,1,'UTF-8') }}
      </div>

      <div class="flex-grow-1">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h5 mb-1 fw-bold">{{ $restaurant['name'] }}</h1>
            <div class="small muted">{{ $restaurant['branch'] }} • {{ $restaurant['eta'] }}</div>
          </div>
          <button class="btn btn-ghost rounded-4 px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartCanvas">
            <i class="bi bi-bag"></i>
            <span class="ms-1" id="cartCountTop">0</span>
          </button>
        </div>

        {{-- Search --}}
        <div class="mt-3">
          <div class="input-group">
            <span class="input-group-text bg-transparent border-0 text-light opacity-75"><i class="bi bi-search"></i></span>
            <input id="searchInput" class="form-control search rounded-4" placeholder="ابحث عن منتج… مثال: لاتيه / تشيز كيك" />
          </div>
        </div>

        <div class="mt-3 d-flex gap-2 flex-wrap">
          <span class="pill"><i class="bi bi-shield-check"></i> طلب سريع</span>
          <span class="pill"><i class="bi bi-lightning-charge"></i> واجهة خفيفة</span>
          <span class="pill"><i class="bi bi-cash-coin"></i> الحد الأدنى: {{ $restaurant['min_order'] }} جنيه</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Categories (horizontal scroll) --}}
  <div class="position-sticky top-0 pb-2" style="z-index:1030;">
    <div class="glass rounded-4 p-2">
      <div class="d-flex gap-2 overflow-auto" style="scrollbar-width:none;">
        <button class="pill active" data-cat="all" type="button"><i class="bi bi-grid"></i> الكل</button>
        @foreach($categories as $cat)
          <button class="pill" data-cat="{{ $cat['id'] }}" type="button">
            <i class="bi {{ $cat['icon'] }}"></i> {{ $cat['name'] }}
          </button>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Items --}}
  <div class="mt-3" id="itemsWrap">
    @foreach($items as $it)
      @php
        $initials = mb_substr($it['name'],0,1,'UTF-8');
        $badge = trim($it['badge'] ?? '');
        $hasMods = !empty($it['mods']);
      @endphp

      <div class="item-card p-3 mb-3"
           data-item
           data-id="{{ $it['id'] }}"
           data-cat="{{ $it['category_id'] }}"
           data-name="{{ e(mb_strtolower($it['name'],'UTF-8')) }}"
           data-price="{{ $it['price'] }}"
           data-mods='@json($it["mods"])'>
        <div class="d-flex gap-3">
          <div class="thumb" aria-hidden="true">{{ $initials }}</div>

          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="fw-bold">{{ $it['name'] }}</div>
                <div class="small muted mt-1">{{ $it['desc'] }}</div>

                <div class="d-flex align-items-center gap-2 mt-2">
                  <div class="fw-bold">{{ $it['price'] }} <span class="small muted">ج</span></div>
                  @if($badge)
                    <span class="badge badge-soft rounded-pill">{{ $badge }}</span>
                  @endif
                  @if($hasMods)
                    <span class="badge rounded-pill text-bg-dark border" style="border-color:rgba(148,163,184,.25)!important;">
                      <i class="bi bi-sliders"></i> خيارات
                    </span>
                  @endif
                </div>
              </div>

              <div class="d-flex flex-column gap-2 align-items-end">
                <button class="btn btn-brand rounded-4 px-3 py-2 addBtn" type="button">
                  <i class="bi bi-plus-lg"></i>
                </button>
                <button class="btn btn-ghost rounded-4 px-3 py-2 detailsBtn" type="button">
                  <i class="bi bi-info-circle"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="safe-space"></div>

  {{-- Sticky Bottom Cart Bar --}}
  <div class="sticky-bottom-bar">
    <div class="container app-shell px-0">
      <div class="glass rounded-4 p-2 d-flex align-items-center justify-content-between">
        <div>
          <div class="fw-bold">السلة <span class="muted small">(<span id="cartCountBottom">0</span>)</span></div>
          <div class="small muted">الإجمالي: <span class="fw-bold text-light" id="cartTotal">0</span> ج</div>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-ghost rounded-4 px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartCanvas">
            عرض
          </button>
          <button class="btn btn-brand rounded-4 px-3" id="whatsBtn" type="button">
            <i class="bi bi-whatsapp"></i> واتساب
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Cart Offcanvas --}}
  <div class="offcanvas offcanvas-bottom text-bg-dark" tabindex="-1" id="cartCanvas" style="height: 78vh; border-top-left-radius:18px;border-top-right-radius:18px;">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title fw-bold"><i class="bi bi-bag"></i> سلة الطلب</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="glass rounded-4 p-3 mb-3">
        <div class="d-flex justify-content-between">
          <div class="muted small">حد أدنى للطلب</div>
          <div class="fw-bold">{{ $restaurant['min_order'] }} ج</div>
        </div>
        <div class="divider my-2"></div>
        <div class="d-flex justify-content-between">
          <div class="muted small">الإجمالي</div>
          <div class="fw-bold"><span id="cartTotal2">0</span> ج</div>
        </div>
      </div>

      <div id="cartList" class="d-grid gap-2"></div>

      <div class="mt-3 glass rounded-4 p-3">
        <label class="form-label muted small">ملاحظات (اختياري)</label>
        <textarea id="orderNotes" class="form-control search rounded-4" rows="2" placeholder="مثال: بدون سكر / بدون بصل …"></textarea>

        <div class="d-grid gap-2 mt-3">
          <button class="btn btn-brand rounded-4 py-2" id="whatsBtn2" type="button">
            <i class="bi bi-whatsapp"></i> اطلب عبر واتساب
          </button>
          <button class="btn btn-ghost rounded-4 py-2" id="clearCartBtn" type="button">
            تفريغ السلة
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Item Details Modal (مع اختيار modifiers بسيط) --}}
  <div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-bg-dark" style="border:1px solid rgba(148,163,184,.18); border-radius:18px;">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="modalTitle">تفاصيل المنتج</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="muted" id="modalDesc"></div>
          <div class="mt-2 fw-bold">السعر: <span id="modalPrice">0</span> ج</div>

          <div id="modsWrap" class="mt-3"></div>

          <button class="btn btn-brand rounded-4 w-100 mt-3" id="modalAddBtn" type="button">
            <i class="bi bi-plus-lg"></i> إضافة للسلة
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
(() => {
  const REST = @json($restaurant);
  const MIN_ORDER = Number(REST.min_order || 0);
  const WHATSAPP = String(REST.whatsapp || '').trim();

  // ---------- Cart (LocalStorage) ----------
  const key = 'ordora_cart_v1';
  const readCart = () => JSON.parse(localStorage.getItem(key) || '{"items":[]}');
  const saveCart = (c) => localStorage.setItem(key, JSON.stringify(c));

  const money = (n) => (Math.round(Number(n||0)*100)/100).toString();

  function cartTotals(cart){
    let count = 0, total = 0;
    cart.items.forEach(i => { count += i.qty; total += (i.price * i.qty); });
    return {count, total};
  }

  function renderBadges(){
    const cart = readCart();
    const {count, total} = cartTotals(cart);
    document.getElementById('cartCountTop').textContent = count;
    document.getElementById('cartCountBottom').textContent = count;
    document.getElementById('cartTotal').textContent = money(total);
    document.getElementById('cartTotal2').textContent = money(total);
  }

  function renderCartList(){
    const cart = readCart();
    const wrap = document.getElementById('cartList');
    wrap.innerHTML = '';

    if(cart.items.length === 0){
      wrap.innerHTML = `
        <div class="glass rounded-4 p-3 muted text-center">
          السلة فارغة… ابدأ بإضافة منتجات 👇
        </div>`;
      renderBadges();
      return;
    }

    cart.items.forEach((it, idx) => {
      const modsText = (it.mods && it.mods.length)
        ? `<div class="small muted mt-1">${it.mods.map(m => `${m.group}: ${m.option}`).join(' • ')}</div>`
        : '';

      const row = document.createElement('div');
      row.className = 'glass rounded-4 p-3';
      row.innerHTML = `
        <div class="d-flex justify-content-between align-items-start gap-2">
          <div class="flex-grow-1">
            <div class="fw-bold">${escapeHtml(it.name)}</div>
            ${modsText}
            <div class="small muted mt-1">${money(it.price)} ج</div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-ghost rounded-4 px-2 py-1" data-act="dec" data-idx="${idx}"><i class="bi bi-dash-lg"></i></button>
            <div class="fw-bold">${it.qty}</div>
            <button class="btn btn-ghost rounded-4 px-2 py-1" data-act="inc" data-idx="${idx}"><i class="bi bi-plus-lg"></i></button>
          </div>
        </div>
      `;
      wrap.appendChild(row);
    });

    renderBadges();
  }

  function addToCart(item, mods = []){
    const cart = readCart();

    // تمييز نفس المنتج مع نفس الـ modifiers
    const signature = JSON.stringify(mods || []);
    const existing = cart.items.find(x => x.id === item.id && JSON.stringify(x.mods||[]) === signature);

    if(existing){
      existing.qty += 1;
    }else{
      cart.items.push({
        id: item.id,
        name: item.name,
        price: Number(item.price),
        qty: 1,
        mods: mods
      });
    }

    saveCart(cart);
    renderCartList();
  }

  function incDec(idx, dir){
    const cart = readCart();
    const it = cart.items[idx];
    if(!it) return;
    it.qty += dir;
    if(it.qty <= 0) cart.items.splice(idx,1);
    saveCart(cart);
    renderCartList();
  }

  function clearCart(){
    saveCart({items:[]});
    renderCartList();
  }

  // ---------- Filtering ----------
  const items = Array.from(document.querySelectorAll('[data-item]'));
  let currentCat = 'all';

  function applyFilters(){
    const q = (document.getElementById('searchInput').value || '').trim().toLowerCase();
    items.forEach(el => {
      const cat = el.getAttribute('data-cat');
      const name = el.getAttribute('data-name');
      const okCat = (currentCat === 'all') || (String(cat) === String(currentCat));
      const okQ = !q || (name && name.includes(q));
      el.style.display = (okCat && okQ) ? '' : 'none';
    });
  }

  document.getElementById('searchInput').addEventListener('input', applyFilters);

  document.querySelectorAll('[data-cat]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-cat]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCat = btn.getAttribute('data-cat');
      applyFilters();
    });
  });

  // ---------- Item Modal (mods) ----------
  const modalEl = document.getElementById('itemModal');
  const modal = new bootstrap.Modal(modalEl);
  let modalItem = null;

  function openModalFromEl(el){
    modalItem = {
      id: Number(el.getAttribute('data-id')),
      name: el.querySelector('.fw-bold')?.textContent?.trim() || '',
      desc: el.querySelector('.muted')?.textContent?.trim() || '',
      price: Number(el.getAttribute('data-price')),
      mods: JSON.parse(el.getAttribute('data-mods') || '[]')
    };

    document.getElementById('modalTitle').textContent = modalItem.name;
    document.getElementById('modalDesc').textContent = modalItem.desc;
    document.getElementById('modalPrice').textContent = money(modalItem.price);

    const modsWrap = document.getElementById('modsWrap');
    modsWrap.innerHTML = '';

    if(!modalItem.mods || modalItem.mods.length === 0){
      modsWrap.innerHTML = `<div class="muted small">لا توجد خيارات لهذا المنتج.</div>`;
      modal.show();
      return;
    }

    modalItem.mods.forEach((g, gi) => {
  const gid = `g_${gi}`;
  const required = !!g.required;
  const multi    = !!g.multi;   // ✅ جديد

  const block = document.createElement('div');
  block.className = 'glass rounded-4 p-3 mb-2';
  block.innerHTML = `
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="fw-bold">${escapeHtml(g.name)}</div>
      ${
        required
          ? `<span class="badge rounded-pill text-bg-warning text-dark">إجباري</span>`
          : `<span class="badge rounded-pill text-bg-secondary">اختياري</span>`
      }
    </div>
    <div class="d-grid gap-2" id="${gid}"></div>
  `;

  modsWrap.appendChild(block);

  const list = block.querySelector('#' + gid);
  (g.options || []).forEach((op, oi) => {
    const delta = Number(op.delta || 0);

    const label = document.createElement('label');
    label.className = 'pill d-flex justify-content-between align-items-center w-100';
    label.style.cursor = 'pointer';

    // لو multi=false نستعمل radio، ولو multi=true نستعمل checkbox
    const inputType = multi ? 'checkbox' : 'radio';
    const inputName = multi ? `mods_${gi}_${oi}` : `mods_${gi}`;

    label.innerHTML = `
      <span class="d-flex align-items-center gap-2">
        <input class="form-check-input m-0"
               type="${inputType}"
               name="${inputName}"
               data-group="${escapeHtml(g.name)}"
               data-option="${escapeHtml(op.name)}"
               data-delta="${delta}">
        <span>${escapeHtml(op.name)}</span>
      </span>
      <span class="small muted">
        ${delta ? ('+' + money(delta) + ' ج') : ''}
      </span>
    `;
    list.appendChild(label);
  });
});


    modal.show();
  }

  document.querySelectorAll('.detailsBtn').forEach(btn => {
    btn.addEventListener('click', (e) => openModalFromEl(e.target.closest('[data-item]')));
  });

  // زر + (لو في options افتح modal، لو لأ ضيف مباشرة)
  document.querySelectorAll('.addBtn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const el = e.target.closest('[data-item]');
      const mods = JSON.parse(el.getAttribute('data-mods') || '[]');
      if(mods && mods.length) openModalFromEl(el);
      else {
        addToCart({
          id: Number(el.getAttribute('data-id')),
          name: el.querySelector('.fw-bold')?.textContent?.trim(),
          price: Number(el.getAttribute('data-price'))
        });
      }
    });
  });

  document.getElementById('modalAddBtn').addEventListener('click', () => {
    if(!modalItem) return;

    // validate required selections
    const requiredGroups = (modalItem.mods || []).filter(g => !!g.required).map(g => g.name);
    const selected = Array.from(modalEl.querySelectorAll('input.form-check-input:checked'));

    // check each required group has at least one selected
    const selectedByGroup = {};
    selected.forEach(i => {
      const g = i.getAttribute('data-group');
      selectedByGroup[g] = selectedByGroup[g] || [];
      selectedByGroup[g].push(i);
    });

    for(const gName of requiredGroups){
      if(!selectedByGroup[gName] || selectedByGroup[gName].length === 0){
        alert('من فضلك اختر: ' + gName);
        return;
      }
    }

    // build mods array + adjust price
    let extra = 0;
    const modsArr = selected.map(i => {
      const delta = Number(i.getAttribute('data-delta') || 0);
      extra += delta;
      return {
        group: i.getAttribute('data-group'),
        option: i.getAttribute('data-option'),
        delta: delta
      };
    });

    addToCart({
      id: modalItem.id,
      name: modalItem.name,
      price: modalItem.price + extra
    }, modsArr);

    modal.hide();
  });

  // ---------- Cart controls ----------
  document.getElementById('cartList').addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-act]');
    if(!btn) return;
    const idx = Number(btn.getAttribute('data-idx'));
    const act = btn.getAttribute('data-act');
    incDec(idx, act === 'inc' ? +1 : -1);
  });

  document.getElementById('clearCartBtn').addEventListener('click', clearCart);

  // ---------- WhatsApp ----------
  function normalizeWhats(phone){
    // يدعم +20... أو 0020... أو 01...
    let p = (phone||'').replace(/\s+/g,'');
    if(p.startsWith('00')) p = '+' + p.slice(2);
    if(p.startsWith('01')) p = '+20' + p.slice(1);
    return p.replace('+','');
  }

  function buildWhatsMessage(){
    const cart = readCart();
    const notes = (document.getElementById('orderNotes')?.value || '').trim();
    const {total} = cartTotals(cart);

    if(cart.items.length === 0) return { ok:false, msg:'السلة فارغة.' };
    if(total < MIN_ORDER) return { ok:false, msg:`الحد الأدنى للطلب ${MIN_ORDER} ج.` };

    let text = `طلب جديد من المنيو ✅\n`;
    text += `المطعم: ${REST.name} - ${REST.branch}\n\n`;
    cart.items.forEach((it, n) => {
      text += `${n+1}) ${it.name} x${it.qty} = ${money(it.price*it.qty)} ج\n`;
      if(it.mods && it.mods.length){
        text += `   خيارات: ${it.mods.map(m => `${m.group}: ${m.option}`).join(' • ')}\n`;
      }
    });
    text += `\nالإجمالي: ${money(total)} ج\n`;
    if(notes) text += `\nملاحظات: ${notes}\n`;
    text += `\nشكراً 🙏`;

    return { ok:true, text };
  }

  function goWhats(){
    const res = buildWhatsMessage();
    if(!res.ok){ alert(res.msg); return; }
    const phone = normalizeWhats(WHATSAPP);
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(res.text)}`;
    window.open(url, '_blank');
  }

  document.getElementById('whatsBtn').addEventListener('click', goWhats);
  document.getElementById('whatsBtn2').addEventListener('click', goWhats);

  // ---------- Utils ----------
  function escapeHtml(s){
    return String(s||'')
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  // init
  renderCartList();
  applyFilters();
})();
</script>
@endpush
