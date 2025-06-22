<div>
    <x-slot:header>الموردون</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>تعديل هذا المورد</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input wire:model.live='supplier.name' type="text" class="form-control" name="name"
                            id="name" aria-describedby="name" placeholder="أدخل اسم المورد" />
                        @error('supplier.name')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">عنوان البريد الإلكتروني</label>
                        <input wire:model.live='supplier.email' type="email" class="form-control" name="email"
                            id="name" aria-describedby="email" placeholder="أدخل عنوان البريد الإلكتروني للمورد" />
                        @error('supplier.email')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">رقم الهاتف</label>
                        <input wire:model.live='supplier.phone_number' type="text" class="form-control" name="phone_number"
                            id="name" aria-describedby="phone_number" placeholder="أدخل رقم الهاتف" />
                        @error('supplier.phone_number')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">رقم التسجيل</label>
                        <input wire:model.live='supplier.registration_number' type="text" class="form-control"
                            name="name" id="name" aria-describedby=""
                            placeholder="أدخل رقم التسجيل التجاري" />
                        @error('supplier.registration_number')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">الرقم الضريبي</label>
                        <input wire:model.live='supplier.tax_id' type="text" class="form-control" name="name"
                            id="name" aria-describedby="" placeholder="أدخل الرقم الضريبي" />
                        @error('supplier.tax_id')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">العنوان الفعلي</label>
                    <textarea wire:model.live='supplier.address' class="form-control" name="" id="" rows="3"></textarea>
                    @error('supplier.address')
                        <small id="" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="" class="form-label">البنك</label>
                    <select wire:model.live='supplier.bank_id' class="form-select " name=""
                        id="">
                        <option selected>اختر البنك</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier.bank_id')
                        <small id="" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">رقم الحساب</label>
                    <input wire:model.live='supplier.account_number' type="text" class="form-control" name="name"
                        id="name" aria-describedby="" placeholder="أدخل رقم حساب المورد" />
                    @error('supplier.account_number')
                        <small id="" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>



            </div>



            <button onclick="confirm('هل أنت متأكد من رغبتك في تحديث هذا المورد؟')||event.stopImmediatePropagation()"
                wire:click='save' class="btn btn-dark text-inv-primary">حفظ</button>
        </div>
    </div>
</div>
