<div>
    <x-slot:header>مراجعة طلب تحويل كهنة</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5 class="mb-0">تفاصيل طلب التحويل الكهنة</h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">المنتج:</label>
                        <p class="form-control-static">{{ $obsoleteConversionRequest->product->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">المخزن:</label>
                        <p class="form-control-static">{{ $obsoleteConversionRequest->warehouse->name }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">الكمية المطلوبة:</label>
                        <p class="form-control-static">{{ $obsoleteConversionRequest->quantity }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">طلب بواسطة:</label>
                        <p class="form-control-static">{{ $obsoleteConversionRequest->requester->name }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">السبب:</label>
                    <p class="form-control-static">{{ $obsoleteConversionRequest->reason }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label">الحالة الحالية:</label>
                    <p class="form-control-static">{{ __('permissions.' . $obsoleteConversionRequest->status) }}</p>
                </div>

                @if ($obsoleteConversionRequest->status === 'pending')
                    <div class="mb-3">
                        <label class="form-label">تغيير الحالة إلى:</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model.live="newStatus" id="statusApproved" value="approved">
                                <label class="form-check-label" for="statusApproved">موافق عليه</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model.live="newStatus" id="statusRejected" value="rejected">
                                <label class="form-check-label" for="statusRejected">مرفوض</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model.live="newStatus" id="statusPending" value="pending">
                                <label class="form-check-label" for="statusPending">معلق</label>
                            </div>
                        </div>
                        @error('newStatus')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">حفظ التغييرات</button>
                @else
                    <div class="alert alert-info mt-4">
                        هذا الطلب ليس في حالة "معلق" ولا يمكن تعديل حالته.
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
