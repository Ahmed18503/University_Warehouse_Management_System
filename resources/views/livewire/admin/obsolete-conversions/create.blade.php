<div>

    
    <div>
        <x-slot:header>إنشاء طلب تحويل لمنتج كهنة</x-slot:header>
        
        <div class="card">
            <div class="card-header bg-inv-secondary text-inv-primary border-0">
                <h5 class="mb-0">طلب تحويل لمنتج كهنة</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
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
                        
                        {{-- Reason --}}
                        <div class="col-md-12">
                            <label class="form-label">السبب</label>
                            <textarea wire:model.live="reason" class="form-control" rows="4" minlength="10"></textarea>
                            @error('reason')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-dark text-inv-primary mt-4">
                        إرسال الطلب
                    </button>
                </form>
            </div>
        </div>
    </div>
    
</div>