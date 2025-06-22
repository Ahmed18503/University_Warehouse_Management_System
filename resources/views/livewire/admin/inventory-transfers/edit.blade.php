@php
    use App\Models\InventoryTransfer;
@endphp

<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>تفاصيل طلب النقل</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">معلومات الطلب</h3>
                        </div>
                        <div class="card-body">
                            <!-- Transfer Details -->
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>المخزن المصدر:</strong> {{ $inventoryTransfer->sourceWarehouse->name }}</p>
                                    <p><strong>المخزن الوجهة:</strong> {{ $inventoryTransfer->destinationWarehouse->name }}</p>
                                    <p><strong>الحالة:</strong> {{ $inventoryTransfer->status }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>تاريخ الإنشاء:</strong> {{ $inventoryTransfer->created_at }}</p>
                                    @if($inventoryTransfer->notes)
                                        <p><strong>ملاحظات:</strong> {{ $inventoryTransfer->notes }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>الكود</th>
                                            <th>الوحدة</th>
                                            <th>النوع</th>
                                            <th>الكمية المطلوبة</th>
                                            @if($inventoryTransfer->status !== InventoryTransfer::STATUS_DRAFT)
                                                <th>الكمية المعتمدة</th>
                                            @endif
                                            @if(in_array($inventoryTransfer->status, [InventoryTransfer::STATUS_SHIPPED, InventoryTransfer::STATUS_RECEIVED, InventoryTransfer::STATUS_COMPLETED]))
                                                <th>الكمية المشحونة</th>
                                            @endif
                                            @if(in_array($inventoryTransfer->status, [InventoryTransfer::STATUS_RECEIVED, InventoryTransfer::STATUS_COMPLETED]))
                                                <th>الكمية المستلمة</th>
                                            @endif
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $index => $item)
                                            <tr>
                                                <td>{{ $item['product_name'] }}</td>
                                                <td>{{ $item['product_code'] }}</td>
                                                <td>{{ $item['unit_name'] }}</td>
                                                <td>{{ $item['item_type'] }}</td>
                                                <td>{{ $item['requested_quantity'] }}</td>
                                                @if($inventoryTransfer->status !== InventoryTransfer::STATUS_DRAFT)
                                                    <td>
                                                        @if($inventoryTransfer->status === InventoryTransfer::STATUS_PENDING_SOURCE_APPROVAL && $isSourceWarehouseUser)
                                                            <input type="number" 
                                                                class="form-control" 
                                                                wire:model.live="items.{{ $index }}.approved_quantity"
                                                                min="0"
                                                                max="{{ $item['requested_quantity'] }}"
                                                            >
                                                            @error("items.{$index}.approved_quantity") 
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        @else
                                                            {{ $item['approved_quantity'] ?? '-' }}
                                                        @endif
                                                    </td>
                                                @endif
                                                @if(in_array($inventoryTransfer->status, [InventoryTransfer::STATUS_SHIPPED, InventoryTransfer::STATUS_RECEIVED, InventoryTransfer::STATUS_COMPLETED]))
                                                    <td>{{ $item['shipped_quantity'] }}</td>
                                                @endif
                                                @if(in_array($inventoryTransfer->status, [InventoryTransfer::STATUS_RECEIVED, InventoryTransfer::STATUS_COMPLETED]))
                                                    <td>{{ $item['received_quantity'] }}</td>
                                                @endif
                                                <td>{{ $item['status'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4">
                                @if($inventoryTransfer->status === InventoryTransfer::STATUS_PENDING_SOURCE_APPROVAL && $isSourceWarehouseUser)
                                    <button wire:click="approveTransfer" class="btn btn-success">
                                        اعتماد النقل
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                        رفض النقل
                                    </button>
                                @endif

                                @if($inventoryTransfer->status === InventoryTransfer::STATUS_SOURCE_APPROVED && $isSourceWarehouseUser)
                                    <button wire:click="shipTransfer" class="btn btn-primary">
                                        شحن النقل
                                    </button>
                                @endif

                                @if($inventoryTransfer->status === InventoryTransfer::STATUS_SHIPPED && $isDestinationWarehouseUser)
                                    <button wire:click="receiveTransfer" class="btn btn-info">
                                        استلام النقل
                                    </button>
                                @endif

                                @if($inventoryTransfer->status === InventoryTransfer::STATUS_RECEIVED && $isDestinationWarehouseUser)
                                    <button wire:click="completeTransfer" class="btn btn-success">
                                        إكمال النقل
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">سبب الرفض</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejectionReason">سبب الرفض</label>
                        <textarea wire:model="rejectionReason" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" wire:click="rejectTransfer">تأكيد الرفض</button>
                </div>
            </div>
        </div>
    </div>
</div> 