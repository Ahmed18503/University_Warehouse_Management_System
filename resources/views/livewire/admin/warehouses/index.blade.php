<div>
    <x-slot:header>المستودعات</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة المستودعات</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover  ">
                <thead class="thead-inverse">
                    <tr>
                        <th>المعرف</th>
                        <th>الاسم</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($warehouses as $warehouse)
                        <tr>
                            <td scope="row">{{ $warehouse->id }}</td>
                            <td>{{ $warehouse->name }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button onclick="confirm('هل أنت متأكد أنك تريد حذف هذا المستودع؟')||event.stopImmediatePropagation()" class="btn btn-danger" wire:click='delete({{ $warehouse->id }})'>
                                    <i class="bi bi-trash-fill"></i>
                                </button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
