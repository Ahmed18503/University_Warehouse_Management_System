<div>
   
    
    <div>
        <x-slot:header>طلبات تحويل المنتجات الكهنة</x-slot:header>
        
        <div class="card">
            <div class="card-header bg-inv-secondary text-inv-primary border-0">
                <h5 class="mb-0">طلبات التحويل المعلقة</h5>
            </div>
            <div class="card-body table-responsive">
                @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <table class="table table-hover">
                    <thead class="thead-inverse">
                        <tr>
                            <th>المعرف</th>
                            <th>المنتج</th>
                            <th>المخزن</th>
                            <th>الكمية المطلوبة</th>
                            <th>السبب</th>
                            <th>طلب بواسطة</th>
                            <th>تاريخ الطلب</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingRequests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>{{ $request->product->name }}</td>
                            <td>{{ $request->warehouse->name }}</td>
                            <td>{{ $request->quantity }}</td>
                            <td>{{ $request->reason }}</td>
                            <td>{{ $request->requester->name }}</td>
                            <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <a wire:navigate href="{{ route('admin.obsolete-conversions.edit', $request->id) }}"
                                class="btn btn-primary btn-sm">
                                مراجعة
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">لا توجد طلبات تحويل كهنة معلقة حاليًا.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
