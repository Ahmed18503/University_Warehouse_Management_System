<div>
    <x-slot:header>المنتجات</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة المنتجات</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover  ">
                <thead class="thead-inverse">
                    <tr>
                        <th>المعرف</th>
                        <th>تفاصيل المنتج</th>
                        <th>الكود</th>
                        <th>الفئة</th>
                        <th>القياس</th>
                        <th>السعر</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td scope="row">{{ $product->id }}</td>
                            <td>
                                <h6>{{ $product->name }}</h6>
                                <small>{{ $product->description }}</small>
                            </td>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                {{ $product->unit->name }}
                            </td>
                            <td>{{ $product->price }} جنيه</td>
                            <!-- <td>
                                {{ $product->inventory_balance }}
                            </td> -->
                            <td class="text-center">
                                <a wire:navigate href="{{ route('admin.products.edit', $product->id) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                    تعديل
                                </a>
                                <button onclick="confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج؟')||event.stopImmediatePropagation()" class="btn btn-danger" wire:click='delete({{ $product->id }})'>
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
