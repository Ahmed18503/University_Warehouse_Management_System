<div>
    <x-slot:header>الأدوار</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة الأدوار</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover  ">
                <thead class="thead-inverse">
                    <tr>
                        <th>المعرف</th>
                        <th>الدور</th>
                        <th>المستخدمين</th>
                        <th class="text-center w-50">الصلاحيات</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td scope="row">{{ $role->id }}</td>
                            <td>{{ $role->title }}</td>
                            <td>{{ count($role->users) }}</td>

                            <td>
                                <ol class="row">
                                    @foreach (json_decode($role->permissions) as $permission)
                                        <li class="col-3">{{ $arabic_permissions[$permission] ?? $permission }}</li>
                                    @endforeach
                                </ol>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                    تعديل
                                </a>
                                <button
                                    onclick="confirm('هل أنت متأكد من رغبتك في حذف هذا الدور؟')||event.stopImmediatePropagation()"
                                    class="btn btn-danger" wire:click='delete({{ $role->id }})'>
                                    <i class="bi bi-trash-fill"></i>
                                    حذف
                                </button>

                                @if ($role->id == 1 && json_decode($role->permissions) != config('permissions.permissions'))
                                    <button
                                        onclick="confirm('هل أنت متأكد من رغبتك في تحديث صلاحيات هذا الدور؟')||event.stopImmediatePropagation()"
                                        class="btn btn-primary" wire:click='updatePermissions({{ $role->id }})'>
                                        <i class="bi bi-arrow-repeat"></i>
                                        تحديث الصلاحيات
                                    </button>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
