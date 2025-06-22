<div>
    <x-slot:header>إنشاء نقل مخزون</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>إنشاء نقل مخزون جديد</h5>
        </div>
        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="source_warehouse" class="form-label">المخزن المصدر</label>
                        <select wire:model.live="inventoryTransfer.source_warehouse_id" id="source_warehouse" 
                            class="form-select">
                            <option value="">اختر المخزن المصدر</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        @error('inventoryTransfer.source_warehouse_id') 
                            <span class="text-danger">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                        <textarea wire:model="notes" id="notes" rows="3" 
                            class="form-control"></textarea>
                        @error('notes') 
                            <span class="text-danger">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-inv-secondary text-inv-primary border-0">
                        <h5>الأصناف المطلوب نقلها</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="product_id" class="form-label">المنتج</label>
                                <select wire:model.live="selectedProduct" id="product_id" class="form-select">
                                    <option value="">اختر المنتج</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
                                    @endforeach
                                </select>
                                @error('selectedProduct') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="transfer_type" class="form-label">النوع</label>
                                <select wire:model.live="transferType" id="transfer_type" class="form-select">
                                    <option value="{{ \App\Models\InventoryTransferItem::TYPE_GOOD }}">جيد</option>
                                    <option value="{{ \App\Models\InventoryTransferItem::TYPE_OBSOLETE }}">تالف</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="quantity" class="form-label">الكمية</label>
                                <input type="number" wire:model="productQuantity" id="quantity" 
                                    class="form-control" min="1">
                                @error('productQuantity') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">الكمية المتاحة</label>
                                <div class="form-control bg-light">
                                    {{ $transferType == \App\Models\InventoryTransferItem::TYPE_GOOD ? $availableGoodQuantity : $availableObsoleteQuantity }}
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" wire:click="addItem" class="btn btn-secondary w-100">
                                    <i class="bi bi-plus-lg"></i>
                                    إضافة صنف
                                </button>
                            </div>
                        </div>

                        @error('items') 
                            <div class="text-danger mb-3">{{ $message }}</div> 
                        @enderror

                        @if (!empty($items))
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المنتج</th>
                                            <th>الكود</th>
                                            <th>النوع</th>
                                            <th>الكمية المطلوبة</th>
                                            <th>الوحدة</th>
                                            <th class="text-center">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $index => $item)
                                            <tr>
                                                <td>{{ $item['product_name'] }}</td>
                                                <td>{{ $item['product_code'] }}</td>
                                                <td>{{ $item['item_type'] == \App\Models\InventoryTransferItem::TYPE_GOOD ? 'جيد' : 'تالف' }}</td>
                                                <td>{{ $item['requested_quantity'] }}</td>
                                                <td>{{ $item['unit_name'] }}</td>
                                                <td class="text-center">
                                                    <button type="button" wire:click="removeItem({{ $index }})" 
                                                        class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash-fill"></i>
                                                        حذف
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                لم يتم إضافة أصناف بعد.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        إنشاء النقل
                    </button>
                    <a href="{{ route('admin.inventory-transfers.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right"></i>
                        عودة للقائمة
                    </a>
                </div>
            </form>
        </div>
    </div>
</div> 