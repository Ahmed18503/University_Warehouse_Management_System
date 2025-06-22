<div>
    <x-slot:header>المستخدمون</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>تعديل هذا المستخدم</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input wire:model.live='user.name' type="text" class="form-control" name="name"
                            id="name" aria-describedby="" placeholder="أدخل اسم المستخدم" />
                        @error('user.name')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">عنوان البريد الإلكتروني</label>
                        <input wire:model.live='user.email' type="email" class="form-control" name="email"
                            id="email" aria-describedby="" placeholder="أدخل عنوان البريد الإلكتروني للمستخدم" />
                        @error('user.email')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group mb-3">
                        <label for="">الأدوار</label>
                        <select wire:model='selectedRoles' multiple class="form-control" name="" id="">
                            @forelse ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->title }}</option>
                            @empty
                                <option disabled>لا توجد أدوار</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group mb-3">
                        <label for="">المخزن الرئيسي</label>
                        @if ($currentUser && $currentUser->id === 1)
                            <select wire:model='warehouse_id' class="form-control" name="" id="">
                                <option value="">اختر المخزن الرئيسي</option>
                                @forelse ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @empty
                                    <option disabled>لا توجد مستودعات</option>
                                @endforelse
                            </select>
                        @else
                            <p class="form-control-static">{{ $user->primaryWarehouse->name ?? 'N/A' }}</p>
                        @endif
                        @error('warehouse_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <button onclick="confirm('هل أنت متأكد من رغبتك في إنشاء هذا المستخدم؟')||event.stopImmediatePropagation()"
                wire:click='save' class="btn btn-dark text-inv-primary">حفظ</button>
        </div>
    </div>
</div>
