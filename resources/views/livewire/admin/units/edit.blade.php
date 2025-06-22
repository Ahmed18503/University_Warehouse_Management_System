<div>
    <x-slot:header>الوحدات</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>تعديل هذه الوحدة</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الوحدة</label>
                        <input wire:model.live='unit.name' type="text" class="form-control" name="name"
                            id="name" aria-describedby="" placeholder="أدخل اسم وحدتك" />
                        @error('unit.name')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="symbol" class="form-label">رمز الوحدة</label>
                        <input wire:model.live='unit.symbol' type="text" class="form-control" name="symbol"
                            id="symbol" aria-describedby="" placeholder="أدخل رمز وحدتك" />
                        @error('unit.symbol')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>



            <button onclick="confirm('هل أنت متأكد من رغبتك في تحديث هذه الوحدة؟')||event.stopImmediatePropagation()"
                wire:click='save' class="btn btn-dark text-inv-primary">حفظ</button>
        </div>
    </div>
</div>
