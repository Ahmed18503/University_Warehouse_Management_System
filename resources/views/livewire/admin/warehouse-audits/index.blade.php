<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تدقيقات المخزون') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-2xl font-bold text-inv-primary">تدقيقات مخزنك</h3>
                        <a href="{{ route('admin.warehouse-audits.create') }}" class="btn btn-primary">بدء تدقيق جديد</a>
                    </div>

                    <div class="mb-4 d-flex flex-wrap gap-3">
                        <input type="date" wire:model.live="searchDate" class="form-control w-auto" placeholder="البحث بالتاريخ">
                        <select wire:model.live="searchStatus" class="form-select w-auto">
                            <option value="">جميع الحالات</option>
                            @foreach ($statuses as $statusCode => $statusName)
                                <option value="{{ $statusCode }}">{{ $statusName }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="perPage" class="form-select w-auto">
                            <option value="10">10 لكل صفحة</option>
                            <option value="25">25 لكل صفحة</option>
                            <option value="50">50 لكل صفحة</option>
                        </select>
                    </div>

                    @if ($audits->isEmpty())
                        <div class="alert alert-info">
                            لا توجد تدقيقات مخزون لمخزنك.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">المخزن</th>
                                        <th scope="col">تاريخ التدقيق</th>
                                        <th scope="col">الحالة</th>
                                        <th scope="col">المدقق</th>
                                        <th scope="col">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($audits as $audit)
                                        <tr>
                                            <td>{{ $audit->warehouse->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($audit->audit_date)->format('Y-m-d') }}</td>
                                            <td><span class="badge {{ $this->getStatusBadgeClass($audit->status) }}">{{ $statuses[$audit->status] ?? $audit->status }}</span></td>
                                            <td>{{ $audit->auditor->name ?? 'غير متوفر' }}</td>
                                            <td>
                                                <a href="{{ route('admin.warehouse-audits.show', $audit->id) }}" class="btn btn-sm btn-info me-2">عرض التفاصيل</a>
                                                <button type="button" wire:click="delete({{ $audit->id }})" wire:confirm="هل أنت متأكد من الغاء هذا التدقيق؟" class="btn btn-sm btn-danger">الغاء</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $audits->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
