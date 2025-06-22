<div>
    <x-slot:header>الادوار</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>إنشاء دور جديد</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الدور</label>
                        <input wire:model.live='role.title' type="text" class="form-control" name="name"
                            id="name" aria-describedby="" placeholder="أدخل اسم الدور" />
                        @error('role.title')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">الصلاحيات</label>
                        <select wire:change="add($event.target.value)" class="form-control">
                            <option value="">اختر صلاحية</option>
                            @foreach ($permissions as $perm)
                                <option value="{{ $perm }}" @if (in_array($perm, $selected_permissions)) disabled @endif>
                                    {{ $arabic_permissions[$perm] ?? $perm }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @foreach ($selected_permissions as $key => $permission)
                        <span class="badge rounded-pill bg-inv-secondary">
                            {{ $arabic_permissions[$permission] ?? $permission }}
                            <a href="javascript:void(0)" wire:click="subtract('{{ $key }}')">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </span>
                    @endforeach
                </div>
            </div>





            <button onclick="confirm('هل أنت متأكد من أنك تريد إنشاء هذا الدور')||event.stopImmediatePropagation()"
                wire:click='save' class="btn btn-dark text-inv-primary">حفظ</button>
        </div>
    </div>
</div>
