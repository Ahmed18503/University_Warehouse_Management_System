<div>
    <x-slot:header>طلباتي</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>طلبات النقل الخاصة بي</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search transfers...">
            </div>

            @if ($myRequests->count() > 0)
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>المخزن المصدر</th>
                            <th>المخزن الوجهة</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>تاريخ الموافقة</th>
                            <th>تاريخ الشحن</th>
                            <th>تاريخ الاستلام</th>
                            <th>تاريخ الإكمال</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($myRequests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->sourceWarehouse->name ?? 'N/A' }}</td>
                                <td>{{ $request->destinationWarehouse->name ?? 'N/A' }}</td>
                                <td>{{ $request->status }}</td>
                                <td>{{ $request->created_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ $request->source_approved_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ $request->shipped_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ $request->received_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ $request->completed_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-center">
                                    @if ($request->status === \App\Models\InventoryTransfer::STATUS_DRAFT)
                                        <button wire:click="cancelTransfer({{ $request->id }})" 
                                            onclick="confirm('هل أنت متأكد من حذف هذا النقل؟')||event.stopImmediatePropagation()"
                                            class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash-fill"></i>
                                            حذف
                                        </button>
                                    @else
                                        <a href="{{ route('admin.inventory-transfers.edit', $request->id) }}" 
                                            class="btn btn-secondary btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                            تعديل
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $myRequests->links() }}
            @else
                <div class="alert alert-info">No transfer requests found.</div>
            @endif
        </div>
    </div>
</div> 