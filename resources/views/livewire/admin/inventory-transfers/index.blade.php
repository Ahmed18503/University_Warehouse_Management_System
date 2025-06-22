<div>
    <x-slot:header>نقل المخزون</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة نقل المخزون</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="thead-inverse">
                    <tr>
                        <th>الرقم</th>
                        <th>المخزن المصدر</th>
                        <th>المخزن الوجهة</th>
                        <th>تم الإنشاء بواسطة</th>
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
                    @foreach ($inventoryTransfers as $transfer)
                        <tr>
                            <td>{{ $transfer->id }}</td>
                            <td>{{ $transfer->sourceWarehouse->name }}</td>
                            <td>{{ $transfer->destinationWarehouse->name }}</td>
                            <td>{{ $transfer->creator->name }}</td>
                            <td>{{ $transfer->status }}</td>
                            <td>{{ $transfer->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $transfer->source_approved_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $transfer->shipped_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $transfer->received_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $transfer->completed_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <a wire:navigate href="{{ route('admin.inventory-transfers.edit', $transfer->id) }}"
                                    class="btn btn-secondary btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                    تعديل
                                </a>
                                @if ($transfer->status === \App\Models\InventoryTransfer::STATUS_DRAFT)
                                    <button onclick="confirm('هل أنت متأكد من حذف هذا النقل؟')||event.stopImmediatePropagation()" 
                                        class="btn btn-danger btn-sm" 
                                        wire:click='delete({{ $transfer->id }})'>
                                        <i class="bi bi-trash-fill"></i>
                                        حذف
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $inventoryTransfers->links() }}
        </div>
    </div>
</div> 