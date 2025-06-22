<div>
    <x-slot:header>مخزون المنتجات بالمخزن</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة المنتجات في هذا المخزن</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="thead-inverse">
                    <tr>
                        <th>ID</th>
                        <th>تفاصيل المنتج</th>
                        <th>الكود</th>
                        <th>الفئة</th>
                        <th>الوحدة</th>
                        <th>السعر</th>
                        <th>الكمية (جيد)</th>
                        <th>الكمية (كهنة)</th>
                        <th>الحد الأدنى</th>
                        <!-- <th class="text-center">إجراءات</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($warehouseProducts as $warehouseProduct)
                        <tr class="{{ $warehouseProduct->quantity_good < $warehouseProduct->min_stock_level ? 'table-warning' : '' }}">
                            <td>{{ $warehouseProduct->product->id }}</td>
                            <td>
                                <h6>{{ $warehouseProduct->product->name }}</h6>
                                <small>{{ $warehouseProduct->product->description }}</small>
                            </td>
                            <td>{{ $warehouseProduct->product->code }}</td>
                            <td>{{ $warehouseProduct->product->category->name }}</td>
                            <td>{{ $warehouseProduct->product->unit->name }}</td>
                            <td>{{ $warehouseProduct->product->price }} جنيه</td>
                            <td>{{ $warehouseProduct->quantity_good }}</td>
                            <td>{{ $warehouseProduct->quantity_obsolete }}</td>
                            <td>{{ $warehouseProduct->min_stock_level }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
