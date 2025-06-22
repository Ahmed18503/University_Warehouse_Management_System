<div>
    <x-slot:header>طلبات النقل المعلقة</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>طلبات النقل المعلقة للموافقة</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search transfers...">
            </div>

            @if ($pendingConfirmations->count() > 0)
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>المخزن المصدر</th>
                            <th>المخزن الوجهة</th>
                            <th>تم الإنشاء بواسطة</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingConfirmations as $transfer)
                            <tr>
                                <td>{{ $transfer->id }}</td>
                                <td>{{ $transfer->sourceWarehouse->name }}</td>
                                <td>{{ $transfer->destinationWarehouse->name }}</td>
                                <td>{{ $transfer->creator->name }}</td>
                                <td>{{ $transfer->status }}</td>
                                <td>{{ $transfer->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-center">
                                    <a wire:navigate href="{{ route('admin.inventory-transfers.edit', $transfer->id) }}"
                                        class="btn btn-secondary btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                        مراجعة
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $pendingConfirmations->links() }}
            @else
                <div class="alert alert-info">No pending confirmations found.</div>
            @endif
        </div>
    </div>

    <!-- Reject Reason Modal -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-inv-secondary text-inv-primary">
                    <h5 class="modal-title" id="rejectReasonModalLabel">Rejection Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Please enter the reason for rejection:</label>
                        <textarea wire:model="rejectionReason" class="form-control" rows="4"></textarea>
                        @error('rejectionReason') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" wire:click="rejectReceipt" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </div>
        </div>
    </div>
</div> 