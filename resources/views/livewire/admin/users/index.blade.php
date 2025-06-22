<div>
    <x-slot:header>المستخدمون</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>قائمة المستخدمين</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover  ">
                <thead class="thead-inverse">
                    <tr>
                        <th>المعرف</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>المخزن الرئيسي</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td scope="row">{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    <li>{{ $role->title }}</li>
                                @endforeach
                            </td>
                            <td>
                                {{ $user->primaryWarehouse->name ?? 'لا يوجد مخزن' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-pencil-square"></i>
                                    تعديل
                                </a>
                                <button onclick="confirm('هل أنت متأكد من رغبتك في حذف هذا المستخدم؟')||event.stopImmediatePropagation()" class="btn btn-danger" wire:click='delete({{ $user->id }})'>
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
