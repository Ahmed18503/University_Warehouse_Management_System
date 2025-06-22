<div>
    <x-slot:header>
        تفاصيل تدقيق المخزون
    </x-slot:header>

    <x-session-message />

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">تدقيق للمخزن: {{ $warehouseAudit->warehouse?->name ?? 'N/A' }}</h5>
                <small class="d-block text-muted">الحالة: <span class="badge {{ $this->getStatusBadgeClass() }}">{{ __('audit_status.' . $warehouseAudit->status, [], 'ar') }}</span></small>
            </div>
            <div class="btn-group" role="group">
                @if ($warehouseAudit->isEditable())
                    <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove>حفظ التقدم</span>
                        <span wire:loading>جاري الحفظ...</span>
                    </button>
                    <button class="btn btn-success" wire:click="completeAudit" wire:confirm="هل أنت متأكد من إكمال هذا التدقيق؟.">
                        <span wire:loading.remove>إكمال التدقيق</span>
                        <span wire:loading>جاري الإكمال...</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body border-bottom">
            <div class="row">
                <div class="col-md-4"><strong>تاريخ التدقيق:</strong> {{ optional($warehouseAudit->audit_date)->format('Y-m-d') ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>المدقق:</strong> {{ $warehouseAudit->auditor?->name ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>تاريخ الإكمال:</strong> {{ optional($warehouseAudit->completed_at)->format('Y-m-d H:i') ?? 'N/A' }}</div>
                <div class="col-12 mt-3"><strong>ملاحظات التدقيق:</strong> {{ $warehouseAudit->notes ?? 'لا يوجد' }}</div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 30%;">المنتج</th>
                        <th scope="col" class="text-center">كمية النظام (جيد)</th>
                        <th scope="col" class="text-center">كمية النظام (كهنة)</th>
                        <th scope="col" class="text-center" style="width: 10%;">الكمية المعدودة (جيد)</th>
                        <th scope="col" class="text-center" style="width: 10%;">الكمية المعدودة (كهنة)</th>
                        <th scope="col" class="text-center">الفروقات</th>
                        <th scope="col" style="width: 20%;">الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @php $isEditable = $warehouseAudit->isEditable(); @endphp
                    @forelse ($warehouseAudit->items as $item)
                        @if($item->product)
                            <tr>
                                <td>
                                    {{ $item->product->name }}
                                    <small class="d-block text-muted">{{ $item->product->code }}</small>
                                </td>
                                <td class="text-center">{{ $item->system_qty_good }}</td>
                                <td class="text-center">{{ $item->system_qty_obsolete }}</td>
                                <td class="text-center">
                                    @if ($isEditable)
                                        <input type="number" class="form-control form-control-sm text-center" wire:model="items.{{ $item->id }}.counted_qty_good">
                                        @error('items.'.$item->id.'.counted_qty_good') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    @else
                                        {{ $item->counted_qty_good }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($isEditable)
                                        <input type="number" class="form-control form-control-sm text-center" wire:model="items.{{ $item->id }}.counted_qty_obsolete">
                                         @error('items.'.$item->id.'.counted_qty_obsolete') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    @else
                                        {{ $item->counted_qty_obsolete }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item->total_discrepancy == 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->total_discrepancy }}
                                    </span>
                                </td>
                                <td>
                                    @if ($isEditable)
                                        <input type="text" class="form-control form-control-sm" wire:model="items.{{ $item->id }}.notes">
                                    @else
                                        {{ $item->notes ?? 'لا يوجد' }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">لم تتم إضافة أي منتجات إلى هذا التدقيق بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
