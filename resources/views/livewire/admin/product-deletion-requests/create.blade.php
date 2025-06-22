<div>
    <x-slot:header>طلب حذف كمية منتج</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>تقديم طلب جديد لحذف كمية منتج</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">المخزن</label>
                            <select wire:model.live="warehouse_id" class="form-select">
                                <option value="">اختر المخزن</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">المنتج</label>
                            <select wire:model.live="product_id" class="form-select">
                                <option value="">اختر منتجًا</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="quantity_to_delete" class="form-label">الكمية المراد حذفها</label>
                    <input type="number" wire:model.live="quantity_to_delete" class="form-control" min="1">
                    @error('quantity_to_delete')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">سبب الحذف</label>
                    <textarea wire:model.live="reason" class="form-control" rows="5"></textarea>
                    @error('reason')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" onclick="confirm('هل أنت متأكد من رغبتك في تقديم طلب حذف كمية المنتج هذا؟')||event.stopImmediatePropagation()" class="btn btn-dark text-inv-primary mt-4">تقديم الطلب</button>
            </form>
        </div>
    </div>
</div> 