<div>
    <x-slot:header>منتجات كل المخازن</x-slot:header>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-inv-secondary text-inv-primary border-0">
            <h5 class="mb-0">قائمة كل المنتجات في كل المخازن</h5>
            <div class="w-50">
                <input type="text" class="form-control" placeholder="بحث باسم المنتج, الكود, أو المخزن..." wire:model.live.debounce.300ms="search">
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="thead-inverse">
                    <tr>
                        <th>المخزن</th>
                        <th>معرف المنتج</th>
                        <th>تفاصيل المنتج</th>
                        <th>الكود</th>
                        <th>الفئة</th>
                        <th>الوحدة</th>
                        <th>السعر</th>
                        <th>الكمية (جيد)</th>
                        <th>الكمية (كهنة)</th>
                        <th>الحد الأدنى للمخزون</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($warehouseProducts as $warehouseProduct)
                        <tr class="{{ $warehouseProduct->quantity_good < $warehouseProduct->min_stock_level ? 'table-warning' : '' }}">
                            <td>{{ $warehouseProduct->warehouse->name }}</td>
                            <td>{{ $warehouseProduct->product->id }}</td>
                            <td>
                                <h6>{{ $warehouseProduct->product->name }}</h6>
                                <small>{{ $warehouseProduct->product->description }}</small>
                            </td>
                            <td>{{ $warehouseProduct->product->sku }}</td>
                            <td>{{ $warehouseProduct->product->category->name }}</td>
                            <td>{{ $warehouseProduct->product->unit->name }}</td>
                            <td>{{ number_format($warehouseProduct->product->price, 2) }} جنيه</td>
                            <td>{{ $warehouseProduct->quantity_good }}</td>
                            <td>{{ $warehouseProduct->quantity_obsolete }}</td>
                            <td>{{ $warehouseProduct->min_stock_level }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">لا توجد منتجات.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $warehouseProducts->links() }}
        </div>
    </div>
</div>
