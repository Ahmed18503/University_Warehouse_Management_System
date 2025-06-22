<div>
    <x-slot:header>تفاصيل طلب حذف المنتج</x-slot:header>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>طلب حذف المنتج #{{ $productDeletionRequest->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>المنتج:</strong> {{ $productDeletionRequest->product->name }} ({{ $productDeletionRequest->product->code }})
                </div>
                <div class="col-md-6">
                    <strong>المخزن:</strong> {{ $productDeletionRequest->warehouse->name }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>الكمية المراد حذفها:</strong> {{ $productDeletionRequest->quantity_to_delete }}
                </div>
                <div class="col-md-6">
                    <strong>طلب بواسطة:</strong> {{ $productDeletionRequest->requester->name }}
                </div>
            </div>
            <div class="mb-3">
                <strong>سبب الحذف:</strong>
                <p>{{ $productDeletionRequest->reason }}</p>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>الحالة:</strong> <span class="badge bg-{{ $productDeletionRequest->status == 'pending' ? 'warning' : ($productDeletionRequest->status == 'approved' ? 'success' : 'danger') }}">{{ ucfirst($productDeletionRequest->status) }}</span>
                </div>
                <div class="col-md-6">
                    <strong>تمت الموافقة/الرفض بواسطة:</strong> {{ $productDeletionRequest->approver->name ?? 'N/A' }}
                </div>
            </div>

            @if ($productDeletionRequest->status === 'pending')
                <hr>
                <h5>الإجراء</h5>
                <div class="mb-3">
                    <label for="newStatus" class="form-label">تغيير الحالة</label>
                    <select wire:model.live="newStatus" class="form-select">
                        <option value="pending">معلق</option>
                        <option value="approved">موافق عليه</option>
                        <option value="rejected">مرفوض</option>
                    </select>
                    @error('newStatus')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" wire:click="save" onclick="confirm('هل أنت متأكد من رغبتك في تحديث حالة هذا الطلب؟')||event.stopImmediatePropagation()" class="btn btn-dark text-inv-primary mt-4">تحديث الحالة</button>
            @else
                <div class="alert alert-info mt-4">
                    تم {{ $productDeletionRequest->status == 'approved' ? 'الموافقة على' : ($productDeletionRequest->status == 'rejected' ? 'رفض' : 'إلغاء') }} هذا الطلب.
                </div>
            @endif
        </div>
    </div>
</div> 