<div>
    <x-slot:header>إضافة كمية وسعر لمنتج في المخزن</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5 class="mb-0">إضافة كمية وسعر</h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row gy-3">
                    {{-- Product --}}
                    <div class="col-md-6">
                        <label class="form-label">اختر المنتج</label>
                        <select wire:model.live="product_id" class="form-select">
                            <option value="">-- اختر --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id') 
                            <div class="text-danger">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Quantity --}}
                    <div class="col-md-6">
                        <label class="form-label">الكمية</label>
                        <input type="number"
                               wire:model.live="quantity"
                               class="form-control"
                               min="1">
                        @error('quantity')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Min Stock --}}
                    <div class="col-md-6">
                        <label class="form-label">الحد الأدنى للكمية</label>
                        <input type="number"
                               wire:model.live="min_stock_level"
                               class="form-control"
                               min="0">
                        @error('min_stock_level')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Price Fields --}}
                    <div class="col-md-6">
                        <label for="current_price" class="form-label">السعر الحالي</label>
                        <input
                            id="current_price"
                            type="number"
                            class="form-control @if(!$use_new_price) border-primary @endif"
                            value="{{ optional($products->firstWhere('id', $product_id))->price ?? 0 }}"
                            wire:click="$set('use_new_price', false)"
                            @if($use_new_price) disabled @endif
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="new_price" class="form-label">السعر الجديد (اختياري)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model.live="use_new_price" id="useNewPriceCheck">
                            <label class="form-check-label" for="useNewPriceCheck">تفعيل السعر الجديد</label>
                        </div>
                        <input
                            id="new_price"
                            type="number"
                            wire:model.live="new_price"
                            class="form-control mt-2 @if($use_new_price) border-primary @endif"
                            step="0.01"
                            min="0"
                            @unless($use_new_price) disabled @endunless
                        >
                        @error('new_price')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-dark text-inv-primary mt-4">
                    إضافة
                </button>
            </form>
        </div>
    </div>
</div>
