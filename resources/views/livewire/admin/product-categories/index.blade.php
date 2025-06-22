<div>
    <x-slot:header>فئات المنتجات</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة فئات المنتجات</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover  ">
                <thead class="thead-inverse">
                    <tr>
                        <th>المعرف</th>
                        <th>الاسم</th>
                        <th>عدد المنتجات</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productCategories as $category)
                        <tr>
                            <td scope="row">{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ count($category->products) }}</td>

                            <td class="text-center">
                                <a href="{{ route('admin.product-categories.edit', $category->id) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button onclick="confirm('هل أنت متأكد من رغبتك في حذف هذه الفئة؟')||event.stopImmediatePropagation()" class="btn btn-danger" wire:click='delete({{ $category->id }})'>
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
