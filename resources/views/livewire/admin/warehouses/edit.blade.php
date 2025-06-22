<div>
    <x-slot:header>المخازن</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>تعديل هذا المخزن</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input wire:model.live='warehouse.name' type="text" class="form-control" name="name"
                            id="name" aria-describedby="" placeholder="أدخل اسم المخزن" />
                        @error('warehouse.name')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>
            <button onclick="confirm('هل أنت متأكد من رغبتك في تعديل هذا المخزن؟')||event.stopImmediatePropagation()"
                wire:click='save' class="btn btn-dark text-inv-primary">حفظ</button>
        </div>
    </div>
</div>
