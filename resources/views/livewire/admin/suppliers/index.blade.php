<div>
    <x-slot:header>الموردون</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة الموردين</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover  ">
                <thead class="thead-inverse">
                    <tr>
                        <th>المعرف</th>
                        <th>التفاصيل الأساسية</th>
                        <th>العنوان</th>
                        <th>تفاصيل العمل</th>
                        <th>تفاصيل الحساب</th>
                        <th>المشتريات التي تمت</th>
                        <th>إجمالي قيمة الشراء</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td scope="row">{{ $supplier->id }}</td>
                            <td>
                                <h6>{{ $supplier->name }}</h6>
                                <small>{{ $supplier->email }}</small><br>
                                <small>{{ $supplier->phone_number }}</small>
                            </td>
                            <td>{{ $supplier->address }}</td>
                            <td>
                                <small><strong>الرقم الضريبي:</strong> {{ $supplier->tax_id }}</small><br>
                                <small><strong>رقم التسجيل:</strong> {{ $supplier->registration_number ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small><strong>البنك:</strong> {{ $supplier->bank->name }}</small><br>
                                <small><strong>رقم الحساب:</strong> {{ $supplier->account_number }}</small>
                            </td>
                            <td>
                                {{ $supplier->purchases->count() }}
                            </td>
                            <td>
                                <small>KES
                                </small>{{ number_format(
                                    $supplier->purchases->sum(function ($sale) {
                                        return $sale->total_amount;
                                    }),
                                ) }}
                            </td>
                            <td class="text-center">
                                <a wire:navigate href="{{ route('admin.suppliers.edit', $supplier->id) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                    تعديل
                                </a>
                                <button
                                    onclick="confirm('هل أنت متأكد من رغبتك في حذف هذا المورد؟')||event.stopImmediatePropagation()"
                                    class="btn btn-danger" wire:click='delete({{ $supplier->id }})'>
                                    <i class="bi bi-trash-fill"></i>
                                    حذف
                                </button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
