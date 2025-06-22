<div>
    <x-slot:header>الوحدات</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة الوحدات</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover  ">
                <thead class="thead-inverse">
                    <tr>
                        <th>ID</th>
                        <th>اسم الوحدة</th>
                        <th>عدد المنتجات</th>
                        <th class="text-center">عمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($units as $unit)
                        <tr>
                            <td scope="row">{{ $unit->id }}</td>
                            <td>{{ $unit->name }}</td>
                            <td>{{ $unit->products->count() }}</td>

                            <td class="text-center">
                                <a href="{{ route('admin.units.edit', $unit->id) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button onclick="confirm('هل انت متاكد من حذف هذه الوحدة ؟')||event.stopImmediatePropagation()" class="btn btn-danger" wire:click='delete({{ $unit->id }})'>
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
