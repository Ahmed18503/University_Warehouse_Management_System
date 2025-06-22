<div>
    <x-slot:header>طلبات حذف المنتج</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة طلبات حذف المنتج</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="thead-inverse">
                    <tr>
                        <th>المعرف</th>
                        <th>المنتج</th>
                        <th>المخزن</th>
                        <th>الكمية المراد حذفها</th>
                        <th>السبب</th>
                        <th>طلب بواسطة</th>
                        <th>الحالة</th>
                        <th>تمت الموافقة بواسطة</th>
                        <th>تاريخ الطلب</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deletionRequests as $request)
                        <tr>
                            <td scope="row">{{ $request->id }}</td>
                            <td>{{ $request->product->name }} ({{ $request->product->code }})</td>
                            <td>{{ $request->warehouse->name }}</td>
                            <td>{{ $request->quantity_to_delete }}</td>
                            <td>{{ Str::limit($request->reason, 50) }}</td>
                            <td>{{ $request->requester->name }}</td>
                            <td>{{ ucfirst($request->status) }}</td>
                            <td>{{ $request->approver->name ?? 'N/A' }}</td>
                            <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <a wire:navigate href="{{ route('admin.product-deletion-requests.edit', $request->id) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                    عرض/تعديل
                                </a>
                                @if (in_array($request->status, ['pending', 'rejected']))
                                <button onclick="confirm('هل أنت متأكد من رغبتك في حذف هذا الطلب؟')||event.stopImmediatePropagation()" class="btn btn-danger" wire:click='delete({{ $request->id }})'>
                                    <i class="bi bi-trash-fill"></i>
                                    حذف
                                </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $deletionRequests->links() }}
        </div>
    </div>
</div> 