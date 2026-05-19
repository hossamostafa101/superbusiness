@if($orders->count())
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>المطعم</th>
                <th>الفرع</th>
                <th>العميل</th>
                <th>نوع الطلب</th>
                <th>الحالة</th>
                <th>الإجمالي</th>
                <th>تاريخ الإنشاء</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $o)
                <tr>
                    <td>{{ $o->id }}</td>
                    <td>{{ $o->restaurant_id }}</td> {{-- تقدر تستبدلها باسم المطعم لو عملت join --}}
                    <td>{{ $o->branch_id }}</td>      {{-- نفس الكلام للفرع --}}
                    <td>{{ $o->customer_name ?? '-' }}</td>
                    <td>{{ $o->order_type }}</td>
                    <td>{{ $o->status }}</td>
                    <td>{{ number_format($o->total ?? 0, 2) }}</td>
                    <td>
                        @if($o->created_at)
                            {{ \Carbon\Carbon::parse($o->created_at)->format('Y-m-d H:i') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-muted text-center py-4">
        لا توجد طلبات في هذه القائمة.
    </div>
@endif
