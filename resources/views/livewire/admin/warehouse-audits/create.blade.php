<div>
    <x-slot:header>إنشاء تدقيق مخزون جديد</x-slot:header>

    <div class="card">
        <div class="card-header">
            <h5>بدء عملية تدقيق جديدة للمخزن: <strong>{{ $warehouse_name }}</strong></h5>
        </div>
        <form wire:submit.prevent="createAudit">
            <div class="card-body">

                <x-session-message />
                
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="notes">ملاحظات (اختياري)</label>
                            <textarea id="notes" class="form-control" wire:model.defer="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="mt-4">اختر المنتجات المراد تدقيقها</h6>
                @error('selectedProducts') <div class="alert alert-danger">{{ $message }}</div> @enderror
                
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-sm table-hover table-bordered">
                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th style="width: 50px;"><input type="checkbox" wire:click="toggleSelectAll($event.target.checked)"></th>
                                <th>المنتج</th>
                                <th>الكود</th>
                                <th class="text-center">الكمية (جيد)</th>
                                <th class="text-center">الكمية (كهنة)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($availableProducts as $wp)
                                <tr>
                                    <td><input type="checkbox" wire:model.live="selectedProducts" value="{{ $wp->product_id }}"></td>
                                    <td>{{ $wp->product_name }}</td>
                                    <td>{{ $wp->product_code }}</td>
                                    <td class="text-center">{{ $wp->quantity_good }}</td>
                                    <td class="text-center">{{ $wp->quantity_obsolete }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد منتجات في هذا المخزن.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>إنشاء تدقيق بـ ({{ count($selectedProducts) }}) منتج</span>
                    <span wire:loading>جاري الإنشاء...</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // JS functionality can go here if needed.
    });
</script>
@endpush
